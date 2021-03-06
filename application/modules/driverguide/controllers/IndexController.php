<?php
class Driverguide_indexController extends Zend_Controller_Action {
	protected $tr = null;
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	//$tr= Application_Form_FrmLanguages::getCurrentlanguage();
    	
	}
	public function indexAction(){
		try{
			$db = new Driverguide_Model_DbTable_DbDriver();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'title' => '',
						'status_search' => -1,
						'province'=>-1,
				);
			}	
			$rs_rows= $db->getAllDriverGuide($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true,null);
			$list = new Application_Form_Frmtable();
			$collumns = array("VEHICLE_TYPE","vehicle_ref_no","Driver's Id","DRIVER_NAME","Gender","TEL","DOB","POB","Nationality","Group No","House No","Street No",
					"Commune","District","Province","STATUS");
			$link=array(
					'module'=>'driverguide','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('car_type'=>$link,'car_number'=>$link, 'driver_id'=>$link,'first_name'=>$link,'last_name'=>$link,'sex'=>$link,'tel'=>$link,'dob'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Location_Form_FrmSearch();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Driverguide_Model_DbTable_DbDriver();
			try{
				$id= $db->addDriver($data);
				if(isset($data['save_new'])){
					$this->_redirect("/driverguide/index/add");
				}
				else{
					$this->_redirect("/driverguide");
				}
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$client_type = $db->getclientdtype();
		array_unshift($client_type,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),) );
		$this->view->clienttype = $client_type;
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmaddGuide();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
		
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_veh_typAsname=$db->getAllVehicleTypeAsName();
		array_unshift($rows_veh_typAsname, array ( 'id' => 0, 'name' =>$this->tr->translate("Choose Vehicle Type")), array ( 'id' => -1, 'name' =>$this->tr->translate("Add Vehicle Type")) );
		$this->view->rows_veh_typasname=$rows_veh_typAsname;
	}
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db_model = new Driverguide_Model_DbTable_DbDriver();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['id']=$id;
			try{
					$id= $db_model->updateDriver($data);
					$this->_redirect("/driverguide");
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$db = new Application_Model_DbTable_DbGlobal();
		$client_type = $db->getclientdtype();
		array_unshift($client_type,array(
				'id' => -1,
				'name' => '$this->tr->translate("ADD_NEW")',
		) );
		$this->view->clienttype = $client_type;
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
		
		$id = $this->getRequest()->getParam("id");
		$row = $db_model->getDriverById($id);
		$this->view->rs = $row;
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmaddGuide($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
	}
	function getvehicleAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Driverguide_Model_DbTable_DbDriver();
			$id = $db_com->getvehicleinfo($data['vehicle']);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	function getreffervehicleAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Driverguide_Model_DbTable_DbDriver();
			$id = $db_com->getReffervehicleinfo($data['vehicle']);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

