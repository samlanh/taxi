<?php
class Bookings_CarrentalController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbCarrental();
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
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RENT_NO","CUSTOMER","RENT_DATE","START_DATE","RETURN_DATE","RETURN_TIME","TOTAL_PAYMENT","TOTAL_RENT_FEE","REFUNDABLE_DEPOSIT","TOTAL_PAID","BALANCE","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'carrental','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('rent_no'=>$link,'name'=>$link));
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
		
		$db = new Bookings_Model_DbTable_DbCarrental();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCarrental($data);
			if(isset($data['save_new'])){
				$this->_redirect("/bookings/carrental/add");
			}else{
				$this->_redirect("/bookings/carrental");
			}
		}
		$frm = new Bookings_Form_FrmCarrental();
		$form = $frm->FormCarrental();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbCarrental();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->updateCarBooking($data);
				$this->_redirect("/bookings/carrental");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCarbookingById($id);
		$this->view->row = $row;
		if (empty($row)){
			$this->_redirect("/bookings/carrental");
		}
		$this->view->driver_info = $db->getDriverInformation($row['driver_id']);
		$this->view->vehicle_info = $db->getvehicleinfo($row['vehicle_id']);
		
		$frm = new Bookings_Form_FrmCarrental();
		$form = $frm->FormCarrental($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	}
	function getcustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCarrental();
			$row = $db->getCustomerInfor($data["customer"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCarrental();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getvehicleinfoAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCarrental();
			$row = $db->getVehicleInfo($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

