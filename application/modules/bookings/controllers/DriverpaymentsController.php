<?php
class Bookings_DriverpaymentsController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'start_date' => date("Y-m-d"),
						'end_date' => date("Y-m-d"),
						'search_text' => "",
						'driver_search'=>0,
				);
			}
			$rs_rows= $db->getAllDriverClearPayment($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","BOOKING_NO","DRIVER_NAME","PAYMENT_DATE","PAYMENT_METHOD","TOTAL_DRIVER_FEE","Total Driver Recieved","PAID","PAID_STATUS","USER_NAME","STATUS",);
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
		$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addDriverPayment($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/driverpayments/add");
			}else{
				$this->_redirect("/bookings/driverpayments");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$frm = new Bookings_Form_FrmDriverPaymentNew();
		$form = $frm->FormBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
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
		$frm = new Bookings_Form_FrmDriverPaymentNew();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getcarbookingbyagencyAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbDriverPaymentNew();
			$id = $db_com->getCarbookingCommissionAgent($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getcarbookingbyagencyeditAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbDriverPaymentNew();
			$id = $db_com->getCarbookingCommissionAgentForEdit($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getDriverPaymentsAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
			$gty= $db->getAllAgentcyBooking($data['driver_id'],$data['type']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
	
	function getAgentcyPaidAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbDriverPaymentNew();
			$gty= $db->getAgencyPayment($data['driver'],$data['row_id'],$data['type']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
}
