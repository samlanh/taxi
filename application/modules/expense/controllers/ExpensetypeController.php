<?php
class Expense_ExpensetypeController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Expense_Model_DbTable_DbExpensetype();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				 
			}
			else{
				$search = array(
					'c_type'=>'',
					'title' => '',
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllCustomerType($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("EXPENSE_TYPE","TITLE","USER_NAME","STATUS");
			$link=array(
					'module'=>'expense','controller'=>'expensetype','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('type_hevicel'=>$link,'account_name'=>$link));
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
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Expense_Model_DbTable_DbExpensetype();
				try{
					$id= $db->addExpenseType($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/expense/expensetype/add");
				}
				else{
					$this->_redirect("/expense/expensetype");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
	}
	
	public function editAction(){
		$db = new Expense_Model_DbTable_DbExpensetype();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
					$db->addExpenseType($data);
					$this->_redirect("/expense/expensetype");
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$id = $this->getRequest()->getParam("id");
		$row = $db->getExpenstype($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/expense/expensetype");
		}
		$this->view->id=$row['id'];
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddExpenType($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
	}
	
	public function addcustomertypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Agency_Model_DbTable_DbAgencytype();
			$data = $this->getRequest()->getPost();
			$id = $db->addCustomerTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

