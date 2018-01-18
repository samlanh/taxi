<?php
class Vehicle_ModelController extends Zend_Controller_Action {
	const REDIRECT_URL_ADD ='/vehicle/model/add';
	const REDIRECT_URL_CLOSE ='/vehicle/model';
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_model = new Vehicle_Model_DbTable_DbModel();
		$rows=$db_model->getAllModel();
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Model","Make Name","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'model','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('title'=>$link,'brand_id'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			//print_r($data);exit();
			try{
			    $db_model=new Vehicle_Model_DbTable_DbModel();
			    if(isset($data['save_new'])){
			    	$db_model->addModel($data);
			    	$this->_redirect(self::REDIRECT_URL_ADD);
// 			    	Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
			    }
			    else if(isset($data['save_close'])){
			    	$db_model->addModel($data);
			    	$this->_redirect(self::REDIRECT_URL_CLOSE);
// 			    	Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
			    }
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' => 'បន្ថែម​អ្នក​ទទួល​ថ្មី') );
		$this->view->all_make=$model;
		$status=$db->getViews();
		$this->view->status_view=$status;
	}
	public function editAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			//print_r($data);exit();
			try{
				$db_model=new Vehicle_Model_DbTable_DbModel();
				if(isset($data['save_close'])){
					$db_model->updateModel($data);
					$this->_redirect(self::REDIRECT_URL_CLOSE);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
	    $id=$this->getRequest()->getParam('id');
	    $db_model=new Vehicle_Model_DbTable_DbModel();
	    $row=$db_model->getModelById($id);
	    $row=$this->view->row=$row;
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' => 'បន្ថែម​អ្នក​ទទួល​ថ្មី') );
		$this->view->all_make=$model;
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
	function addMakeAction(){
		if($this->getRequest()->isPost()){
				$_data = $this->getRequest()->getPost();
				$_dbmodel = new Application_Model_DbTable_DbGlobal();
				$id = $_dbmodel->ajaxaddMake($_data);
				print_r(Zend_Json::encode($id));
				exit();
		}
	}	
	function addModelAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Application_Model_DbTable_DbGlobal();
			$id = $_dbmodel->ajaxaddModel($_data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

