<?php
class Vehicle_typeController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	const REDIRECT_URL_ADD ='/vehicle/type/add';
	const REDIRECT_URL_ADD_CLOSE ='/vehicle/type/';
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_make = new Vehicle_Model_DbTable_DbType();
		
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
			
			$rows=$db_make->getAllType($search);
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgActive($rows, BASE_URL, true);
			
			$list = new Application_Form_Frmtable();
			$collumns = array("TYPE","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'type','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('status'=>$link,'type'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm =$frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data= $this->getRequest()->getPost();
			try {
				$db_make = new Vehicle_Model_DbTable_DbType();
				if(!empty($data['save_new'])){
					$db_make->addType($data);
					$this->_redirect(self::REDIRECT_URL_ADD);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}else{
					$db_make->addType($data);
					$this->_redirect(self::REDIRECT_URL_ADD_CLOSE);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
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
	public function editAction(){
		if($this->getRequest()->isPost()){
			$data= $this->getRequest()->getPost();
// 			print_r($data);exit();
			try {
				$db_type = new Vehicle_Model_DbTable_DbType();
					$db_type->updateType($data);
					$this->_redirect(self::REDIRECT_URL_ADD_CLOSE);
// 				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
		$id=$this->getRequest()->getParam('id');
		$db_type = new Vehicle_Model_DbTable_DbType();
		$row=$db_type->getTypeById($id);
		$this->view->row=$row;
	}
	public function addtypeAction(){
		if($this->getRequest()->isPost()){
			$db = new Vehicle_Model_DbTable_DbType();
			$data = $this->getRequest()->getPost();
			$id = $db->addTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

