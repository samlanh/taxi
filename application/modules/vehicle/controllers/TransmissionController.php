<?php
class Vehicle_TransmissionController extends Zend_Controller_Action {
	const REDIRECT_URL_ADD ='/vehicle/transmission/add';
	const REDIRECT_URL_ADD_CLOSE ='/vehicle/transmission/';
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_tran = new Vehicle_Model_DbTable_DbTransmission();
		$rows=$db_tran->getAllTransmission();
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Transmission Name","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'transmission','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('status'=>$link,'tran_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
			
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			try {
			$db_tran = new Vehicle_Model_DbTable_DbTransmission();
			if(!empty($data['save_new'])){
				$db_tran->addTransmission($data);
				$this->_redirect(self::REDIRECT_URL_ADD);
// 				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
			}else {
				$db_tran->addTransmission($data);
				$this->_redirect(self::REDIRECT_URL_ADD_CLOSE);
// 				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
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
			$data=$this->getRequest()->getPost();
			try {
			$db_tran = new Vehicle_Model_DbTable_DbTransmission();
// 			if(!empty($data['save_close'])){
				$db_tran->updateTransmission($data);
				$this->_redirect(self::REDIRECT_URL_ADD_CLOSE);
// 				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
// 			}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id=$this->getRequest()->getParam('id');
		$db_tran = new Vehicle_Model_DbTable_DbTransmission();
		$row=$db_tran->getTransmissionById($id);
		$this->view->row=$row;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
	}
}

