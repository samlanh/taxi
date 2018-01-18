<?php
class location_LocationController extends Zend_Controller_Action {
	const REDIRECT_URL ='/location';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				$search =$_data;
			}else{
		
				$search = array(
						'service_type'=>0,
						'title' => '',
						'status_search' => -1,
						'location_type' => -1
				);
		
			}
			$db = new Location_Model_DbTable_DbLocation();
			$rs_rows= $db->getAllLocations($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("Location Name","Province","Service Type","Location Type","Modify Date","STATUS");
			$link=array(
					'module'=>'location','controller'=>'location','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('location_name'=>$link,'province_name'=>$link));
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
			try {
				$db = new Location_Model_DbTable_DbLocation();
				$data = $this->getRequest()->getPost();
				$db->addPackage($data);
				if(!empty($data['save_new'])){
					$this->_redirect(self::REDIRECT_URL."/location/add");
	// 				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/location/add");
				}else{
					$this->_redirect(self::REDIRECT_URL."/location");
	// 				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/index");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocation();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
	}
	public function editAction(){
		$db_model = new Location_Model_DbTable_DbLocation();
		
		if($this->getRequest()->isPost()){
			try {
			$data = $this->getRequest()->getPost();
			$db_model->updatePackage($data);
			$this->_redirect(self::REDIRECT_URL."/location");
// 				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/location");
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Location_Model_DbTable_DbProvince();
		$this->view->provincelist = $db->getAllProvince();
		$id = $this->getRequest()->getParam("id");
		$row = $db_model->getLocationById($id);;
		$this->view->row = $row;
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",self::REDIRECT_URL."/locationtype");
		}
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocation($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
}

