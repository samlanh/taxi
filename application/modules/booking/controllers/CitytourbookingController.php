<?php
class Booking_CitytourbookingController extends Zend_Controller_Action {
	private $activelist = array('áž˜áž·áž“áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹', 'áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Booking_Model_DbTable_DbCityTourBooking();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'from_book_date' => $formdata['from_book_date'],
						'to_book_date' => $formdata['to_book_date'],
						'search_text'=>$formdata['search_text'],
				);
			}
			else{
				$search = array(
						'to_book_date' => date("Y-m-d"),
						'from_book_date' => date("Y-m-d"),
						'search_text' => "",
				);
			}
			$rs_rows= $db->getAllCityTourBooking($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Booking No","Customer Name","Date Book","Pickup Date","Return Date","Total Fee","Total Payment",);
			$link=array(
					'module'=>'booking','controller'=>'index','action'=>'view',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('booking_no'=>$link,'customer'=>$link,));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Booking_Form_FrmSearchBooking();
		$frm =$frm->FormSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	function addAction(){
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbCityTourBooking();
			$booking_id=$db->addProductRental($data);
			//$db_mail->sendInvoiceEmail($booking_id);
			 
			$session =new Zend_Session_Namespace('cityTourbooking');
			$session->unsetAll();
			Application_Form_FrmMessage::redirectUrl("/booking/citytourbooking/add");
		}
		
		$session =new Zend_Session_Namespace('cityTourbooking');
		$this->view->bookinginfo = $session;
		$frm = new Booking_Form_FrmBooking();
		$form = $frm->FromBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getVehicleAvailableAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$row=$db->createSessionBookingCityTour($post,2);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	
	function getvehicleselectedAction(){//action step2
		$vehicle_id = $this->getRequest()->getParam("id");
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($vehicle_id){
		
			$db->createSessionBookingCityTour($vehicle_id,3);
			$this->_redirect("booking/citytourbooking/add");
		}
		//Application_Form_FrmMessage::redirectUrl("/index/testing");
	}
	function getGuideSelectedAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionBookingCityTour($post,4);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	function getProductSelectedAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionBookingCityTour($post,4);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	function otherFeeAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionBookingCityTour($post,5);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	function customerInfoAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionBookingCityTour($post,6);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	function confirmBookingAction(){
		$db = new Booking_Model_DbTable_DbCityTourBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionBookingCityTour($post,7);
			$this->_redirect("booking/citytourbooking/add");
		}
	}
	
	function getCustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbCityTourBooking();
			$row = $db->getNameCustomer($data["id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getlocationbyproidAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbCityTourBooking();
			$row = $db->getLocationByProId($data["id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}	
	function getcitytourpackageidAction(){//action step 1
		$package_id = $this->getRequest()->getParam("package_id");
		//print_r($package_id);exit();
		if($package_id){
			$dbbooking = new Booking_Model_DbTable_DbCityTourBooking();
			$dbbooking->createSessionBookingCityTour($package_id,1);
			$this->_redirect("booking/citytourbooking/add");
	
		}else{
			$this->_redirect("index/citytour");
		}
	}

}

