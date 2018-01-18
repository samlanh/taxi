<?php
class location_PackageController extends Zend_Controller_Action {
	const REDIRECT_URL ='/location';
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
			$db = new Location_Model_DbTable_DbLocation();
			$rs_rows= $db->getAllPackages($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
		
			$list = new Application_Form_Frmtable();
			$collumns = array("Package Name","Location Name","Modify Date","STATUS");
			$link=array(
					'module'=>'location','controller'=>'package','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('location_name'=>$link,'location'=>$link));
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
			$data=$this->getRequest()->getPost();
			try {
				$db = new Location_Model_DbTable_DbLocation();
				$data = $this->getRequest()->getPost();
				$db->addNewPackageLocation($data);
				if(!empty($data['save_new'])){
					$this->_redirect(self::REDIRECT_URL."/package/add");
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/package/add");
				}else{
					$this->_redirect(self::REDIRECT_URL."/package");
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/package");
				}
			} catch (Exception $e) {
				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}
		
		$frm = new Location_Form_FrmPackage();
		$frm=$frm->FrmAddPackage();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
// 		$db = new Application_Model_GlobalClass();
// 		$this->view->pro_option = $db->getAllProvinceOption();
// 		$db = new Location_Model_DbTable_DbProvince();
// 		$this->view->provincelist = $db->getAllProvince();
	}
	public function editAction(){
		$db = new Location_Model_DbTable_DbLocation();
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			try {
				
				$data = $this->getRequest()->getPost();
				$db->updateNewPackageLocation($data);
				$this->_redirect(self::REDIRECT_URL."/package");
// 				if(!empty($data['save_new'])){
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/package/add");
// 				}else{
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/package");
// 				}
			} catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
// 				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}
		
		$id = $this->getRequest()->getParam("id");
		$row = $db->getLocationById($id);
		$this->view->locationdetail = $db->getAllLocationByPackage($id);
		
// 		$db = new Application_Model_GlobalClass();
// 		$this->view->pro_option = $db->getAllProvinceOption();
		
// 		$db = new Location_Model_DbTable_DbProvince();
// 		$this->view->provincelist = $db->getAllProvince();
		$this->view->row = $row;
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",self::REDIRECT_URL."/package");
		}
		$frm = new Location_Form_FrmPackage();
		$frm=$frm->FrmAddPackage($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
	
	function getLocationAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_db = new Application_Model_DbTable_DbGlobal();
			$opt = !empty($_data['opt'])?$_data['opt']:null;
			$locations = $_db->getAllLocationByProvince($_data['province_id']);
			print_r(Zend_Json::encode($locations));
			exit();
		}
	}
}

