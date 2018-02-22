<?php
class Bookings_DriverpaymentController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbDriverPayment();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date' 		=> date("Y-m-d"),
						'from_book_date' 	=> date("Y-m-d"),
						'search_text' 		=> "",
						'driver_search'		=> 0,
				);
			}
			$rs_rows= $db->getAllDriverPayment($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","DRIVER","PAYMENT_DATE","PAYMENT_METHOD","BALANCE","PAID","TOTAL_DUE","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'driverpayment','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('payment_no'=>$link,'driver_name'=>$link));
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
		$db = new Bookings_Model_DbTable_DbDriverPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCommissionPayment($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/driverpayment/add");
			}else{
				$this->_redirect("/bookings/driverpayment");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$frm = new Bookings_Form_FrmDriverPayment();
		$form = $frm->FormBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$id=$this->getRequest()->getParam('id');
		$db = new Bookings_Model_DbTable_DbDriverPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['driver_payment_id']=$id;
			$booking_id=$db->updateCommissionPayment($data);
				$this->_redirect("/bookings/driverpayment");
		}
		$this->view->id = $id;
		$row = $db->getCommissionPaymentByID($id);
		$this->view->row =$row;
		if (empty($row)){
			$this->_redirect("/bookings/driverpayment");
		}
		$frm = new Bookings_Form_FrmDriverPayment();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getdriverAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbDriverPayment();
			$row = $db->getDriverInfor($data["driver_id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getcarbookingdriverinfoAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbDriverPayment();
			$id = $db_com->getCarbookingDriverIfo($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getcarbookingbyagencyeditAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbDriverPayment();
			$id = $db_com->getCarbookingCommissionAgentForEdit($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
}

