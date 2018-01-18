<?php
class Group_customertypeController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbCustomertype();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'title' => $formdata['title'],
						'status_search'=>$formdata['status_search'],
						);
			}
			else{
				$search = array(
					'title' => '',
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllCustomerType($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","STATUS");
			$link=array(
					'module'=>'group','controller'=>'customertype','action'=>'edit',
			);
			
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('name_en'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$frm = new Group_Form_FrmClient();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Group_Model_DbTable_DbCustomertype();
				try{
					$id= $db->addCustomerType($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/group/customertype/add");
				}
				else{
					$this->_redirect("/group/customertype");
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
		$db = new Group_Model_DbTable_DbCustomertype();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
					$db->addCustomerType($data);
					$this->_redirect("/group/customertype");
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$id = $this->getRequest()->getParam("id");
		$row = $db->getCustomerTypeById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/group/customertype");
		}
		$this->view->id=$row['id'];
		$fm = new Group_Form_FrmCustype();
		$frm = $fm->FrmAddCustomerType($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
	}
	public function addcustomertypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbCustomertype();
			$data = $this->getRequest()->getPost();
			$id = $db->addCustomerTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

