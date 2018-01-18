<?php
class Location_IndexController extends Zend_Controller_Action {
	const REDIRECT_URL ='/location';
	protected $tr;
	public function init()
	{
		/* Initialize action controller here */
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
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
			$db = new Location_Model_DbTable_DbProvince();
			$rs_rows= $db->getAllProvince($search);
		
			$glClass = new Application_Model_GlobalClass();
			$rs = $glClass->getImgActive($rs_rows, BASE_URL, true,null);
		
			$list = new Application_Form_Frmtable();
			$collumns = array("PROVINCE","DATE","STATUS","BY_USER");
			$link=array(
					'module'=>'location','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs,array('province_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Location_Form_FrmSearch();
		$frm =$frm->search();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	function addAction()
	{
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel = new Location_Model_DbTable_DbProvince();				
				if(!empty($_data['save_new'])){
					$_dbmodel->addNewProvince($_data);
					$this->_redirect("/location/index/add");
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL."/index/add");
				}else{
					$_dbmodel->addNewProvince($_data);
					$this->_redirect("/location/index");
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL."/index/index");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$fm=new Location_Form_FrmProvince();
		$frm_province=$fm->FrmProvince();
		Application_Model_Decorator::removeAllDecorator($frm_province);
		$this->view->frm_province = $frm_province;
	}
	function editAction(){
		$id=$this->getRequest()->getParam("id");
		$db=new Location_Model_DbTable_DbProvince();
		$row=$db->getProvinceById($id);
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			try {
			$db->updateProvince($data,$id);
				$this->_redirect("/location/index");
// 				Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL . "/index/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm= new Location_Form_FrmProvince();
		$update=$frm->FrmProvince($row);
		$this->view->frm_province=$update;
		Application_Model_Decorator::removeAllDecorator($update);
		
	    $this->view->row = $row;
	}
}
