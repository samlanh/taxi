<?php
class Booking_CarrentalbookingController extends Zend_Controller_Action {
	private $activelist = array('áž˜áž·áž“áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹', 'áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	function addAction(){
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbBooking();
			$booking_id=$db->addProductRental($data);
			//$db_mail->sendInvoiceEmail($booking_id);
			 
			$session =new Zend_Session_Namespace('carRentalbooking');
			$session->unsetAll();
			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		
		$session =new Zend_Session_Namespace('carRentalbooking');
		$this->view->bookinginfo = $session;
		$frm = new Booking_Form_FrmBooking();
		$form = $frm->FromBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getVehicleAvailableAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$row=$db->createSessionCarRental($post,1);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	
	function getvehicleselectedAction(){//action step2
		$vehicle_id = $this->getRequest()->getParam("id");
		$db = new Booking_Model_DbTable_DbBooking();
		if($vehicle_id){
			$db->createSessionCarRental($vehicle_id,2);
			$this->_redirect("booking/carrentalbooking/add");
		}
		//Application_Form_FrmMessage::redirectUrl("/index/testing");
	}
	function getGuideSelectedAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionCarRental($post,3);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	function getProductSelectedAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionCarRental($post,4);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	
	function customerInfoAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionCarRental($post,6);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	function otherFeeAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionCarRental($post,5);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	function confirmBookingAction(){
		$db = new Booking_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionCarRental($post,7);
			$this->_redirect("booking/carrentalbooking/add");
		}
	}
	
	function getCustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbBooking();
			$row = $db->getNameCustomer($data["id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function indexAction(){
		try{
			$db = new Booking_Model_DbTable_DbCarRental();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date' => date("Y-m-d"),
						'from_book_date' => date("Y-m-d"),
						'search_text' => "",
						'customer'=>-1,
				);
			}
			$rs_rows= $db->getAllCarBooking($search);
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

}

