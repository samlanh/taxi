<?php
class Expense_VehicleMaintenanceController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Expense_Model_DbTable_DbVehicleMaintenance();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
					'title' => '',
					'status_search' => -1,
					'agencytype_id' => -1
				);
			}
			$rs_rows= $db->getAllClients($search);
			 
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("CUS_CODE","First Name","Last Name","Gender","Customer Type","DOB","PHONE","POB","Nationality","Company Name","Group No",
					"House No","Commune","District","Province","STATUS");
			$link=array(
					'module'=>'agency','controller'=>'index','action'=>'edit',
			);
			
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('customer_code'=>$link,'first_name'=>$link,'last_name'=>$link,'sex'=>$link,'custype'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Expense_Form_FrmClient();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	public function addAction(){
		$db = new Expense_Model_DbTable_DbVehicleMaintenance();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				try{
					$id= $db->addClient($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/agency/index/add");
				}
				else{
					$this->_redirect("/agency");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row=$this->view->opt_type=$db->getAllExpenseByType(1);
		 
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$customer_opt= $dbgb->getViewsAsName(10);
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($customer_opt,array('id' => 0,'name' =>"",),array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->custype = $customer_opt;
		
		$fm = new Expense_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		$fr =new Expense_Form_FrmVehiclemaintenance();
		$frm_h=$fr->addVehicleMaintenance();
		Application_Model_Decorator::removeAllDecorator($frm);
		Application_Model_Decorator::removeAllDecorator($fr);
		$this->view->frm_client = $frm;
		$this->view->frm_h = $frm_h;
	}
	
	public function editAction(){
		$db = new Expense_Model_DbTable_DbVehicleMaintenance();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
					$db->addClient($data);
				$this->_redirect("/agency");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$fm = new Expense_Form_FrmClient();
		$id = $this->getRequest()->getParam("id");
		$row = $db->getClientById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/agency");
		}
		$this->view->row = $row;
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$customer_opt= $dbgb->getViewsAsName(10);
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($customer_opt,array('id' => 0,'name' =>"",),array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->custype = $customer_opt;
		
		$this->view->id=$row['id'];
		$frm = $fm->FrmAddClient($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
	}
	
	 
	function getDriverInfoAction(){
		if($this->getRequest()->isPost()){
			$db = new Expense_Model_DbTable_DbVehicleMaintenance();
			$data = $this->getRequest()->getPost();
			$code = $db->getVehiclAndDriver($data['vehicle_id']);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
}

