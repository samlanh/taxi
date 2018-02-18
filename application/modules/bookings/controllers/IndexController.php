<?php
class Bookings_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	//protected $tr = null;
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	 $tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbBooking();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'to_book_date'   => date("Y-m-d"),
						'from_book_date' => date("Y-m-d"),
						'search_text'    => "",
						'customer'       =>-1,
						'working_status' =>-1
				);
			}
			$rs_rows= $db->getAllCarBooking($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("BOOKING_NO","CUSTOMER_NAME","FROM_LOCATION","TO_LOCATION","BOOKING_DATE","DELIVERY_DATE","CAR_RENT_FEE","COMMISSION_FEE","OTHER_FEE","GRAND_TOTAL","DRIVER","DRIVER_FEE","BOOKING_STATUS","STATUS",);
			$link=array(
					'module'=>'bookings','controller'=>'index','action'=>'edit',
			);
			$book_status=array(
					'module'=>'bookings','controller'=>'index','action'=>'bookview',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('booking_no'=>$link,'cus_name'=>$link,'book_status'=>$book_status));
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
		////////////////////////////////add popup/////////
		$fm = new Agency_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		$db_globle=new Application_Model_DbTable_DbGlobal();
		$row_cu = $db_globle->getAllCustomers();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($row_cu,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->cus_all = $row_cu;
		$fm = new Agency_Form_FrmClient();
		$agency=$db_globle->getAllAgency();
		array_unshift($agency,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->agency_all = $agency;
		
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocation();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_location = $frm;
		$local=$db_globle->getAllLocation();
		array_unshift($local,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->location_all = $local;
		array_unshift($local,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$row=$this->view->service_booking=$db->getAllServiceType();
		$ser=$db->getAllServiceoption();
		array_unshift($ser,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->services=$ser;
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddService();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_service = $frm;
	}
	
	public function editAction()
	{
		$db = new Bookings_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->updateCarBooking($data);
				$this->_redirect("/bookings/index");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCarbookingById($id);
		 
		$this->view->row = $row;
		if (empty($row)){
			$this->_redirect("/bookings/index");
		}
		//$this->view->driver_info = $db->getDriverInformation($row['driver_id']);
		$frm = new Bookings_Form_FrmCarBooking();
		$form = $frm->FormBooking($row);
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
		$this->view->ser_detail=$db->getServiceDetail($id);
		
		////////////////////////////////add popup/////////
		$fm = new Agency_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		$db_globle=new Application_Model_DbTable_DbGlobal();
		$row_cu = $db_globle->getAllCustomers();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($row_cu,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->cus_all = $row_cu;
		$fm = new Agency_Form_FrmClient();
		$agency=$db_globle->getAllAgency();
		array_unshift($agency,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->agency_all = $agency;
		
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocation();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_location = $frm;
		$local=$db_globle->getAllLocation();
		array_unshift($local,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->location_all = $local;
		array_unshift($local,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$row=$this->view->service_booking=$db->getAllServiceType();
		$ser=$db->getAllServiceoption();
		array_unshift($ser,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->services=$ser;
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddService();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_service = $frm;
	}
	
	public function bookviewAction()
	{
		$id=$this->getRequest()->getParam('id');
		$db = new Bookings_Model_DbTable_DbBooking();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['id']=$id;
			$booking_id=$db->addDrivert($data);
			$this->_redirect("/bookings/index");
		}
		$id=$this->getRequest()->getParam('id');
		$this->view->id = $id;
		$row = $db->getCarbookingById($id);
			
		$this->view->row = $row;
		if (empty($row)){
			$this->_redirect("/bookings/index");
		}
		//$this->view->driver_info = $db->getDriverInformation($row['driver_id']);
		//$this->view->vehicle_info = $db->getvehicleinfo($row['vehicle_id']);
		
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
	
	function addcustomerAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			//echo $data;exit();
			$code = $db->addCustomerAjax($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
	function addagencyAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->addAgencyAjax($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
	function addlocationAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->addLocationAjax($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
	function addserviceAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->addServiceAjax($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
}

