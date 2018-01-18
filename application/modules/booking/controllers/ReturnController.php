<?php
class Booking_ReturnController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
	}
	public function indexAction(){
		$db = new Booking_Model_DbTable_DbReturnBook();
		 
		if($this->getRequest()->isPost()){
			$formdata=$this->getRequest()->getPost();
			$search = array(
					'adv_search'=> $formdata["adv_search"],
					'book_no' => $formdata['book_no'],
					'date_book'=>$formdata['date_book'],
// 					'pickup_date'=>$formdata['pickup_date'],
// 					'return_date'=>$formdata['return_date'],
			);
		}
		else{
			$search = array(
					'book_no' => -1,
					'adv_search' => "",
					'date_book'=> date('Y-m-d'),
// 					'pickup_date'=>date('Y-m-d'),
// 					'return_date'=>date('Y-m-d'),
					'status'=>-1,);
		}
		$row = $db->getAllBooking($search);
		$this->view->row_booking = $row;
		 
		$form = new Booking_Form_FrmReturnBooking();
		$frm = $form->FrmReturnBooking();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frms = $frm;
	}
	public function returndetailAction(){
		$id = $this->getRequest()->getParam("id");
		$db= new Booking_Model_DbTable_DbReturnBook();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db->ReturnBook($id);
                        Application_Form_FrmMessage::Sucessfull("Return was Succesfull ", "/booking/return");
		}
		
		$row = $db->getBookingById($id, 1);
		$row_deatil = $db->getBookingById($id, 2);
		
		$this->view->row = $row;
		$this->view->row_detail = $row_deatil;
	}
	
	public function editAction(){
		
	}
}

