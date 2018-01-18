<?php
class Other_TaxController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
			}
				else{
			
				}
				$db = new Other_Model_DbTable_DbTax();
				$rs = $db->getAllTax();
				$glClass = new Application_Model_GlobalClass();
				$rs = $glClass->getImgActive($rs, BASE_URL, true);
				$list = new Application_Form_Frmtable();
				$collumns = array("Tax Title","Tax Value","Status");
				$link=array(
						'module'=>'other','controller'=>'tax','action'=>'edit',
				);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			$this->view->list=$list->getCheckList(0, $collumns, $rs,array('title'=>$link,'value'=>$link));
			
		}
	
	function addAction()
		{
			if($this->getRequest()->isPost()){//check condition return true click submit button
				$_data = $this->getRequest()->getPost();
				$_dbmodel = new Other_Model_DbTable_DbTax();
				try {
					$_dbmodel->addtax($_data);
					if(!empty($_data['save_new'])){
						$this->_redirect(self::REDIRECT_URL . "/tax/add");
// 						Application_Form_FrmMessage::message("INSERT_SUCCESS");
					}else{
						$this->_redirect(self::REDIRECT_URL . "/tax");
// 						Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL . "/tax");
					}
				}catch (Exception $e) {
					Application_Form_FrmMessage::message("INSERT_FAIL");
					$err =$e->getMessage();
					Application_Model_DbTable_DbUserLog::writeMessageError($err);
				}
			}
			$db = new Application_Model_DbTable_DbGlobal();
			$status=$db->getViews();
			$this->view->status_view=$status;
		}
		function editAction()
		{
			$_db = new Other_Model_DbTable_DbTax();
			if($this->getRequest()->isPost()){//check condition return true click submit button
				$_data = $this->getRequest()->getPost();
				$_db = new Other_Model_DbTable_DbTax();
				try {
					$_db->updateTax($_data);
					$this->_redirect(self::REDIRECT_URL . "/tax");
// 					Application_Form_FrmMessage::Sucessfull(("EDIT_SUCCESS"),self::REDIRECT_URL . "/tax");
				}catch (Exception $e) {
					Application_Form_FrmMessage::message("INSERT_FAIL");
					$err =$e->getMessage();
					Application_Model_DbTable_DbUserLog::writeMessageError($err);
				}
			}
			$id = $this->getRequest()->getParam("id");
			$this->view->row = $_db->getTaxById($id);
			
			$db = new Application_Model_DbTable_DbGlobal();
			$status=$db->getViews();
			$this->view->status_view=$status;
		}
}

