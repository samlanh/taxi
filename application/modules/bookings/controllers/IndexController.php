<?php
class Bookings_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbBooking();
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
			$collumns = array("BOOKING_NO","CUSTOMER","FROM_LOCATION","TO_LOCATION","BOOKING_DATE","DELIVERY_DATE","CAR_RENT_FEE","COMMISSION_FEE","OTHER_FEE","GRAND_TOTAL","DRIVER","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('booking_no'=>$link,'cus_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Bookings_Form_FrmSearchBooking();
		$frm =$frm->FormSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	public function addAction(){
		
		$db = new Bookings_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCarBooking($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/index/add");
			}else{
				$this->_redirect("/bookings/index");
			}
// 			Application_Form_FrmMessage::redirectUrl("/booking/carrentalbooking/add");
		}
		$frm = new Bookings_Form_FrmCarBooking();
		$form = $frm->FormBooking();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCarBooking($data);
				$this->_redirect("/bookings/index");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCarbookingById($id);
		if (empty($row)){
			$this->_redirect("/bookings/index");
		}
		$frm = new Bookings_Form_FrmCarBooking();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	function getcustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBooking();
			$row = $db->getCustomerInfor($data["customer"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBooking();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getdrivercarAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbBooking();
			$row = $db->getDriverAndCarInfor($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

