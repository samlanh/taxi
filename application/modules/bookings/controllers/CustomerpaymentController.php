<?php
class Bookings_CustomerpaymentController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbCustomerPayment();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date' => date("Y-m-d"),
						'from_book_date' => date("Y-m-d"),
						'search_text' => "",
						'customer'=>0,
				);
			}
			$rs_rows= $db->getAllCarbookingPayment($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","CUSTOMER","PAYMENT_DATE","PAYMENT_METHOD","BALANCE","PAID","TOTAL_DUE","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'customerpayment','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('payment_no'=>$link,'payment_date'=>$link,'customer'=>$link));
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
		$db = new Bookings_Model_DbTable_DbCustomerPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCarbookingPayment($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/customerpayment/add");
			}else{
				$this->_redirect("/bookings/customerpayment");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$frm = new Bookings_Form_FrmCustomerPayment();
		$form = $frm->FormCustomerPayment();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbCustomerPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->updateCarbookingPayment($data);
				$this->_redirect("/bookings/customerpayment");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCarbookingPaymentByID($id);
		$this->view->row =$row;
		if (empty($row)){
			$this->_redirect("/bookings/customerpayment");
		}
		$frm = new Bookings_Form_FrmCustomerPayment();
		$form = $frm->FormCustomerPayment($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	function getcarbookingbycustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbCustomerPayment();
			$id = $db_com->getCarbookingPayment($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	function getcarbookingbycustomereditAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbCustomerPayment();
			$id = $db_com->getCarbookingPaymentForEdit($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
}

