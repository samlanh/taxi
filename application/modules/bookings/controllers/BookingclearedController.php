<?php
class Bookings_BookingclearedController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	protected $tr = null;
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbBookingCleared();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date'   => date("Y-m-d"),
						'from_book_date' => date("Y-m-01"),
						'search_text'    => "",
						'customer'       =>0,
						'working_status' =>-1,
						'date_type'		 =>'2',
						'agency_search'	 =>'0',
						'vehicle_type'	 =>'0',
						'driver_search'  =>0,
						
						'start_time'  =>'',
						'delivery_time'  =>'',
						'agency_search'  =>0,
						'status'       =>1,
				);
			}
			$this->view->search=$search;
			$rs_rows= $db->getAllBookingClearedPayment($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","DRIVER_NAME","PAYMENT_DATE","PAYMENT_METHOD","TOTAL_DRIVER_FEE","Total Driver Recieved","PAID","PAID_STATUS","USER_NAME","STATUS","ACTION");
			$link=array(
					'module'=>'bookings','controller'=>'driverpayments','action'=>'edit',
			);
			$link_print=array( 
					'module'=>'report','controller'=>'bookingpayment','action'=>'rpt-driver-paymentdetail',
			);
			$print=$this->tr->translate("PRINT");
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('payment_no'=>$link,'driver_name'=>$link,'payment_date'=>$link,$print=>$link_print,));
			$this->view->rows=$rs_rows;
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
		$db = new Bookings_Model_DbTable_DbBookingCleared();
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
		$id=$this->getRequest()->getParam('id');
		$db = new Bookings_Model_DbTable_DbBookingCleared();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->updateDriverPayment($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/driverpayments/add");
			}else{
				$this->_redirect("/bookings/driverpayments");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$row=$db->getDriverClearById($id);
		$row_detail=$db->getDriverClearDetail($id);
		$this->view->row=$row;
		$this->view->rows=$row_detail;
		$frm = new Bookings_Form_FrmDriverPaymentNew();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBookingCleared();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getcarbookingbyagencyAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbBookingCleared();
			$id = $db_com->getCarbookingCommissionAgent($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getcarbookingbyagencyeditAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Bookings_Model_DbTable_DbBookingCleared();
			$id = $db_com->getCarbookingCommissionAgentForEdit($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getDriverPaymentsAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBookingCleared();
			$gty= $db->getAllAgentcyBooking($data['driver_id'],$data['type']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
	
	function getAgentcyPaidAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBookingCleared();
			$gty= $db->getAgencyPayment($data['driver'],$data['row_id'],$data['type']);
			print_r(Zend_Json::encode($gty));
			exit();
		}
	
	}
	
	function driverInfoAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBookingCleared();
			$row = $db->getDriverInfor($data["driver_id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}

