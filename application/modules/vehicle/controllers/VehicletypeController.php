<?php
class Vehicle_VehicletypeController extends Zend_Controller_Action {
	const REDIRECT_URL_ADD ='/vehicle/vehicletype/add';
	const REDIRECT_URL_ADD_CLOSE ='/vehicle/vehicletype/';
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_make = new Vehicle_Model_DbTable_DbVehicleType();
		
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
				);
			}
			$rows=$db_make->getAllVehicleType($search);
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgActive($rows, BASE_URL, true);
			
			$list = new Application_Form_Frmtable();
			$collumns = array("VEHICLETYPE","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'vehicletype','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('status'=>$link,'title'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm =$frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	function addAction(){
		if($this->getRequest()->isPost()){
			$data= $this->getRequest()->getPost();
		//print_r($data);exit();    
			try {
				$db_vehicletype = new Vehicle_Model_DbTable_DbVehicleType();
				if(!empty($data['save_new'])){
					$db_vehicletype->addVehicleType($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}else{
					$db_vehicletype->addVehicleType($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
	}
	function editAction(){
		if($this->getRequest()->isPost()){
			$data= $this->getRequest()->getPost();
// 			print_r($data);exit();
			try {
				$db_vehicletype = new Vehicle_Model_DbTable_DbVehicleType();
				if(!empty($data['save_close'])){
					$db_vehicletype->updateVehicleType($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id=$this->getRequest()->getParam('id');
		$db_vehicletype = new Vehicle_Model_DbTable_DbVehicleType();
		$row=$db_vehicletype->getVehicleTypeById($id);
		$this->view->row=$row;
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
	}
	function addvehiceltypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Vehicle_Model_DbTable_DbVehicleType();
			$data = $this->getRequest()->getPost();
			$id = $db->addVehicleTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

