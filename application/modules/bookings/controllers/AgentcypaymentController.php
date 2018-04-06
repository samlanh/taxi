<?php
class Bookings_AgentcypaymentController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbAgentcyPayment();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date' => date("Y-m-d"),
						'from_book_date' => date("Y-m-d"),
						'search_text' => "",
						'agency_search'=>0,
				);
			}
			$rs_rows= $db->getAllCommissionPayment($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","AGENCY","PAYMENT_DATE","PAYMENT_METHOD","BALANCE","PAID","TOTAL_DUE","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'agentcypayment','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('payment_no'=>$link,'agentcy'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Bookings_Form_FrmSearchBooking();
		$frm =$frm->FormSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	public function addAction()
	{
		$db = new Bookings_Model_DbTable_DbAgentcyPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCommissionPayment($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/agentcypayment/add");
			}else{
				$this->_redirect("/bookings/agentcypayment");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$frm = new Bookings_Form_FrmAgentcyPayment();
		$form = $frm->FormBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbAgentcyPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->updateCommissionPayment($data);
				$this->_redirect("/bookings/agentcypayment");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCommissionPaymentByID($id);
		$this->view->row =$row;
		if (empty($row)){
			$this->_redirect("/bookings/agentcypayment");
		}
		$frm = new Bookings_Form_FrmAgentcyPayment();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbAgentcyPayment();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getcarbookingbyagencyAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbAgentcyPayment();
			$id = $db_com->getCarbookingCommissionAgent($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getcarbookingbyagencyeditAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbAgentcyPayment();
			$id = $db_com->getCarbookingCommissionAgentForEdit($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getAgentcyPaymentsAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbAgentcyPayment();
			$gty= $db->getAllAgentcyBooking($data['agency_id']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
	
	function getAgentcyPaidAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbAgentcyPayment();
			$gty= $db->getAgencyPayment($data['agency']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
}

