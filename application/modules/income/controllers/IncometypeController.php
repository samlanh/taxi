<?php
class Income_IncometypeController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Income_Model_DbTable_DbIncometype();
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
			$rs_rows= $db->getAllIncomeType($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("INCOME_TYPE","DESCRIPTION","STATUS");
			$link=array(
					'module'=>'income','controller'=>'incometype','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link,'disc'=>$link));
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
				$db = new Income_Model_DbTable_DbIncometype();
				try{
					$id= $db->addIncomeType($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/income/incometype/add");
				}
				else{
					$this->_redirect("/income/incometype/");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->frmIncomeType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$data['id']=$id;
				$db = new Income_Model_DbTable_DbIncometype();
				try{
					$id= $db->addIncomeType($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/income/incometype/add");
				}
				else{
					$this->_redirect("/income/incometype/");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db=new Income_Model_DbTable_DbIncometype();
		$row=$db->getIncometype($id);
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->frmIncomeType($row);
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

