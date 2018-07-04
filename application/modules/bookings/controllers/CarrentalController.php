<?php
class Bookings_CarrentalController extends Zend_Controller_Action {
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
	        $db = new Bookings_Model_DbTable_DbCustomerCarrental();
	        if($this->getRequest()->isPost()){
	            $search=$this->getRequest()->getPost();
	        }
	        else{
	            $search = array(
	                'start_date'   => date("Y-m-01"),
	                'end_date'     => date("Y-m-d"),
	                'search_text'  => "",
	                'lessee_name'  => 0,
	                'vehicle_type' => -1,
	                'plate_number' => 0,
	                'is_return'	   => -1,
	                'status'       => '',
	            );
	        }
	        $rs_rows= $db->getAllCarrental($search);
	        $glClass = new Application_Model_GlobalClass();
	        $rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
	        $list = new Application_Form_Frmtable();
	        $collumns = array("Paid Date","Rent Number","Vehicle Type","Vehicle Ref No.","Color","Price/Month","Rent Date","Return Date","Deposit","Action","Status","User");
	        $link=array(
	            'module'=>'bookings','controller'=>'index','action'=>'edit',
	        );
	        $book_status=array(
	            'module'=>'bookings','controller'=>'index','action'=>'bookview',
	        );
	        //$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('booking_no'=>$link,'cus_name'=>$link,'book_status'=>$book_status));
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
	
	public function addAction(){
	    $db = new Bookings_Model_DbTable_DbCustomerCarrental();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$booking_id=$db->addCarrental($data);
			if(isset($data['save_new'])){
			    Application_Form_FrmMessage::redirectUrl("/bookings/carrental/add");
			}else{
			    Application_Form_FrmMessage::redirectUrl("/bookings/carrental");
			}
			Application_Form_FrmMessage::redirectUrl("/bookings/carrental");
		}
		$frm = new Bookings_Form_FrmCarrental();
		$form = $frm->FormRenntCarental();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
		
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_veh_typ=$db->getAllVehicleTypestore();
		array_unshift($rows_veh_typ,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
		$this->view->vehicle_type=$rows_veh_typ;
		$db_cus=new Application_Model_DbTable_DbGlobal();
		$rs=$db_cus->getAllCustomer();
		array_unshift($rs,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
		$this->view->cusrow=$rs;
	}
	
	public function editAction()
	{
	    $id=$this->getRequest()->getParam('id');
	    $db = new Bookings_Model_DbTable_DbCustomerCarrental();
	    if($this->getRequest()->isPost()){
	        $data = $this->getRequest()->getPost();
	        $data['id']=$id;
	        $booking_id=$db->updateCarrental($data);
	        if(isset($data['save_new'])){
	            Application_Form_FrmMessage::redirectUrl("/bookings/carrental/index");
	        }else{
	            Application_Form_FrmMessage::redirectUrl("/bookings/carrental/index");
	        }
	        Application_Form_FrmMessage::redirectUrl("/bookings/carrental");
	    }
	    $row=$db->getCarrentalById($id);
	    $pic_id=$db->getIdDetailByCarr($id);
	    $this->view->pic_all=$db->getImgBongById($pic_id);
	    $frm = new Bookings_Form_FrmCarrental();
	    $form = $frm->FormRenntCarental($row);
	    Application_Model_Decorator::removeAllDecorator($form);
	    $this->view->frm = $form;
	    
	    $db=new Vehicle_Model_DbTable_DbVehicle();
	    $rows_veh_typ=$db->getAllVehicleTypestore();
	    array_unshift($rows_veh_typ,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
	    $this->view->vehicle_type=$rows_veh_typ;
	    
	    $db_cus=new Application_Model_DbTable_DbGlobal();
	    $rs=$db_cus->getAllCustomer();
	    array_unshift($rs,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
	    $this->view->cusrow=$rs;
	}
	
	function getcustomerAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCustomerCarrental();
			$row = $db->getAllCustomerById($data["cus_id"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getvehicleinforAction(){
	    if($this->getRequest()->isPost()){
	        $data = $this->getRequest()->getPost();
	        $db = new Bookings_Model_DbTable_DbCustomerCarrental();
	        $row = $db->getVehcleInfor($data["vehicle_id"]);
	        print_r(Zend_Json::encode($row));
	        exit();
	    }
	}
	
	function getagentAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCustomerCarrental();
			$row = $db->getAgencyInfor($data["agency"]);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getvehicleinfoAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Bookings_Model_DbTable_DbCustomerCarrental();
			$row = $db->getVehcleInfor($data['vehicle_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function addvehicletypeAction(){
	    if($this->getRequest()->isPost()){
	        $db = new Bookings_Model_DbTable_DbCustomerCarrental();
	        $data = $this->getRequest()->getPost();
	        $code = $db->addAVehicleType($data);
	        print_r(Zend_Json::encode($code));
	        exit();
	    }
	}
	
	function addcustomersAction(){
	    if($this->getRequest()->isPost()){
	        $db = new Bookings_Model_DbTable_DbCustomerCarrental();
	        $data = $this->getRequest()->getPost();
	        $code = $db->addCustomer($data);
	        print_r(Zend_Json::encode($code));
	        exit();
	    }
	}
	
}

