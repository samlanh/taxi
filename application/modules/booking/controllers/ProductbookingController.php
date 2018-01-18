<?php
class Booking_ProductbookingController extends Zend_Controller_Action {
	private $activelist = array('áž˜áž·áž“áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹', 'áž”áŸ’ážšáž¾â€‹áž”áŸ’ážšáž¶ážŸáŸ‹');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
public function indexAction(){
		try{
			$db = new Booking_Model_DbTable_DbProductBooking();
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
			$rs_rows= $db->getAllProductBooking($search);
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
			$db = new Booking_Model_DbTable_DbProductBooking();
			$booking_id=$db->addProductRental($data);
			//$db_mail->sendInvoiceEmail($booking_id);
			 
			$session =new Zend_Session_Namespace('Productlbooking');
			$session->unsetAll();
			Application_Form_FrmMessage::redirectUrl("/booking/productbooking/add");
		}
		
		$session =new Zend_Session_Namespace('Productlbooking');
		$this->view->bookinginfo = $session;
		$frm = new Booking_Form_FrmBooking();
		$form = $frm->FromBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	function getProductAvailableAction(){
		$db = new Booking_Model_DbTable_DbProductBooking();
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$row=$db->createSessionProduct($post,1);
			$this->_redirect("booking/productbooking/add");
		}
	}
	
	function getProductSelectedAction(){
		$db = new Booking_Model_DbTable_DbProductBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionProduct($post,2);
			$this->_redirect("booking/productbooking/add");
		}
	}
	function otherFeeAction(){
		$db = new Booking_Model_DbTable_DbProductBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionProduct($post,3);
			$this->_redirect("booking/productbooking/add");
		}
	}
	function customerInfoAction(){
		$db = new Booking_Model_DbTable_DbProductBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionProduct($post,4);
			$this->_redirect("booking/productbooking/add");
		}
	}
	function confirmBookingAction(){
		$db = new Booking_Model_DbTable_DbProductBooking();
		if($this->getRequest()->isPost()){
			$post = $this->getRequest()->getPost();
			$db->createSessionProduct($post,5);
			$this->_redirect("booking/productbooking/add");
		}
	}
	
	function getCustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Booking_Model_DbTable_DbProductBooking();
			$row = $db->getNameCustomer($data["id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}

}

