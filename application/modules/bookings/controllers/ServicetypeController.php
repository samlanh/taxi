<?php
class Bookings_ServicetypeController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbServiceType();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				 
			}
			else{
				$search = array(
					'title' => '',
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllServiceType($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("SERVICE_TYPE","NOTE","DATE","USER_NAME","STATUS");
			$link=array(
					'module'=>'bookings','controller'=>'servicetype','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title_en'=>$link,'note'=>$link));
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
				$db = new Bookings_Model_DbTable_DbServiceType();
				try{
					$id= $db->addSericeType($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/bookings/servicetype/add");
				}
				else{
					$this->_redirect("/bookings/servicetype");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
	}
	
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Bookings_Model_DbTable_DbServiceType();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['id']=$id;
			try{
					$db->addSericeType($data);
					$this->_redirect("/bookings/servicetype");
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row = $db->getServicetype($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/bookings/servicetype");
		}
		$this->view->id=$row['id'];
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddCustomerType($row);
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

