<?php
class Bookings_ServiceController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Bookings_Model_DbTable_DbService();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				 
			}
			else{
				$search = array(
					'title' => '',
					'service_type'=>'',
					'status_search' => -1,
				);
			}
			$rs_rows= $db->getAllService($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("SERVICE_NAME","SERVICE_TYPE","NOTE","DATE","USER_NAME","STATUS");
			$link=array(
					'module'=>'bookings','controller'=>'service','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('service_title'=>$link,'service_id'=>$link));
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
				$db = new Bookings_Model_DbTable_DbService();
				try{
					$id= $db->addSerice($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/bookings/service/add");
				}
				else{
					$this->_redirect("/bookings/service");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddService();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_serice = $frm;
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
		$servic=new Bookings_Model_DbTable_DbService();
		$rows= $servic->getSerictTypeOpt();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($rows,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->ser_type=$rows;
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		$db = new Bookings_Model_DbTable_DbService();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$data['id']=$id;
				try{
					$id= $db->addSerice($data);
				 if(isset($data['save_new'])){
				 	$this->_redirect("/bookings/service/index");
				}
				else{
					$this->_redirect("/bookings/service");
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row=$db->getServiceById($id);
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddService($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_serice = $frm;
		
		$fm = new Bookings_Form_FrmServiceType();
		$frm = $fm->FrmAddCustomerType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_custype = $frm;
		$servic=new Bookings_Model_DbTable_DbService();
		$rows= $servic->getSerictTypeOpt();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($rows,array('id' => -1,'name' => $tr->translate("ADD_NEW"),));
		$this->view->ser_type=$rows;
	}
	
	public function addservicetypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Bookings_Model_DbTable_DbService();
			$data = $this->getRequest()->getPost();
			$id = $db->addServiceTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

