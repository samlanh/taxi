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
					'payment_method'=>'',
					'vehicle_id'	=>-1,
					'start_date'=>date("Y-m-d"),
					'end_date'=>date("Y-m-d"),
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllExpenseMaintenance($search);
			 
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns =  array("INVOICE","CHEQUE_NO","VEHICLE_REF_NO","PAYMENT_TYPE","TITLE","TOTALE","CTEATE_DATE","USER_NAME","STATUS");
					 
			$link=array(
					'module'=>'expense','controller'=>'vehiclemaintenance','action'=>'edit',
			);
			
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('car_no'=>$link,'invoice'=>$link,'cheque_no'=>$link,'payment_type'=>$link,'title'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Expense_Form_FrmSearchInfo();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	public function addAction(){
		$db = new Expense_Model_DbTable_DbVehicleMaintenance();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				try{
					$id= $db->addExpense($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/expense/vehiclemaintenance/add");
				}
				else{
					$this->_redirect("/expense/vehiclemaintenance/index");
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
		
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_cat_type = $frm;
		$db=new Expense_Model_DbTable_DbExpensetype();
		$type=$db->getExpenstypeByOpt(1);
		array_unshift($type,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->cat_type=$type;
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		$db = new Expense_Model_DbTable_DbVehicleMaintenance();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$data['id']=$id;
				try{
					$id= $db->updateExpenseMaintenance($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/expense/vehiclemaintenance/index");
				}
				else{
					$this->_redirect("/expense/vehiclemaintenance/index");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row=$this->view->opt_type=$db->getAllExpenseByType(1);
		$rowbyid=$db->getExpenseMainById($id);
		$this->view->exp_detail=$db->getExpenseDetail($id);
		 
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$customer_opt= $dbgb->getViewsAsName(10);
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($customer_opt,array('id' => 0,'name' =>"",),array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->custype = $customer_opt;
		
		$fm = new Expense_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		$fr =new Expense_Form_FrmVehiclemaintenance();
		$frm_h=$fr->addVehicleMaintenance($rowbyid);
		Application_Model_Decorator::removeAllDecorator($frm);
		Application_Model_Decorator::removeAllDecorator($fr);
		$this->view->frm_client = $frm;
		$this->view->frm_h = $frm_h;
		
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_cat_type = $frm;
		$db=new Expense_Model_DbTable_DbExpensetype();
		$type=$db->getExpenstypeByOpt(1);
		array_unshift($type,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->cat_type=$type;
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
	
	function getCarInfoAction(){
		if($this->getRequest()->isPost()){
			$db = new Expense_Model_DbTable_DbVehicleMaintenance();
			$data = $this->getRequest()->getPost();
			$code = $db->getVehicleInfo($data['vehicle_id']);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
	function addCatcoryTypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Expense_Model_DbTable_DbExpensetype();
			$data = $this->getRequest()->getPost();
			$code = $db->addCategoryType($data,1);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
}

