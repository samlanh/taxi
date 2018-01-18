<?php
class Location_LocationtypeController extends Zend_Controller_Action {
	const REDIRECT_URL='/location';
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
				$search = array(
						'title' => $_data['title'],
						'status' => $_data['status_search']);
			}
			else{
		
				$search = array(
						'title' => '',
						'status' => -1,
				);
		
			}
			$db = new Location_Model_DbTable_DbLocationtype();
			$rs_rows= $db->getAllLocationType($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("Location Type","Modify Date","STATUS");
			$link=array(
					'module'=>'location','controller'=>'locationtype','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link,'date'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Location_Form_FrmSearch();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	public function addAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel = new Location_Model_DbTable_DbLocationtype();
				if(!empty($_data['save_new'])){
					$_dbmodel->addLocationType($_data);
					$this->_redirect(self::REDIRECT_URL."/locationtype/add");
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/locationtype/add");
				}else{
					$_dbmodel->addNewProvince($_data);
					$this->_redirect(self::REDIRECT_URL."/locationtype");
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/locationtype");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocationType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
	public function editAction(){
		$_db= new Location_Model_DbTable_DbLocationtype();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_db->updateLocationType($_data);
				$this->_redirect(self::REDIRECT_URL."/locationtype");
// 				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS",self::REDIRECT_URL."/locationtype");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id = $this->getRequest()->getParam("id");
		$row = $_db->getLocationTypeById($id);
		$this->view->row = $row;
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",self::REDIRECT_URL."/locationtype");
		}
		
		$frm = new Location_Form_FrmLocation();
		$frm=$frm->FrmAddLocationType($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
}

