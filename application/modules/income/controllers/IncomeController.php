<?php
class Income_IncomeController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group';
	protected $tr;
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
		$this->tr= Application_Form_FrmLanguages::getCurrentlanguage();
	}
	
	public function indexAction(){
		try{
			$db = new Income_Model_DbTable_DbIncome();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
					'title' => '',
					'payment_method'=>'',
					'start_date'=>date("Y-m-01"),
					'end_date'=>date("Y-m-d"),
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllIncome($search);
			 
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("INVOICE","CHEQUE_NO","PAYMENT_TYPE","TITLE","TOTALE","CTEATE_DATE","USER_NAME","STATUS");
			$link=array(
					'module'=>'income','controller'=>'income','action'=>'edit',
			);
			
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('invoice'=>$link,'cheque_no'=>$link,'payment_type'=>$link,'title'=>$link));
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
		$db = new Income_Model_DbTable_DbIncome();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				try{
					$id= $db->addIncome($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/income/income/add");
				}
				else{
					$this->_redirect("/income/income/index");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		 
		$fr =new Expense_Form_FrmVehiclemaintenance();
		$frm_h=$fr->frmIncome();
		Application_Model_Decorator::removeAllDecorator($frm_h);
		$this->view->frm_h = $frm_h;
		$this->view->rs_type=$db->getAllIncomeType();
		
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_cat_type = $frm;
		
		$db=new Income_Model_DbTable_DbIncometype();
		$type=$db->getIncometypeByOpt();
		array_unshift($type,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
		$this->view->cat_type=$type;
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		$db = new Income_Model_DbTable_DbIncome();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['id']=$id;
			try{
				$id= $db->updateIncome($data);
				if(isset($data['save_new'])){
					$this->_redirect("/income/income/add");
				}
				else{
					$this->_redirect("/income/income/index");
				}
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row=$db->getIncomeById($id);
		$this->view->row_detail=$db->getIncomeDetail($id);
		$fr =new Expense_Form_FrmVehiclemaintenance();
		$frm_h=$fr->frmIncome($row);
		Application_Model_Decorator::removeAllDecorator($frm_h);
		$this->view->frm_h = $frm_h;
		$this->view->rs_type=$db->getAllIncomeType();
		
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_cat_type = $frm;
		
		$db=new Income_Model_DbTable_DbIncometype();
		$type=$db->getIncometypeByOpt();
		array_unshift($type,array('id' => -1,'name' => $this->tr->translate("ADD_NEW"),));
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
	
	function addCatcoryTypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Income_Model_DbTable_DbIncometype();
			$data = $this->getRequest()->getPost();
			$code = $db->addCategoryType($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	
}

