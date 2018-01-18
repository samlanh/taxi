<?php
class Vehicle_SubmodelController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	const REDIRECT_URL_ADD ='/vehicle/submodel/add';
	const REDIRECT_URL_CLOSE ='/vehicle/submodel/index';
	
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_model = new Vehicle_Model_DbTable_DbSubModel();
		$rows=$db_model->getAllSubModel();
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Make Name","Model","Sub Model","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'submodel','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('title'=>$link,'model_id'=>$link,'make_id'=>$link));
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
				$db_model=new Vehicle_Model_DbTable_DbSubModel();
				if(isset($data['save_new'])){
					$db_model->addSubmodel($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}
				else if(isset($data['save_close'])){
					$db_model->addSubmodel($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
	//	$db = new Vehicle_Model_DbTable_DbMake();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' => 'បន្ថែម​អ្នក​ទទួល​ថ្មី') );
		$this->view->allmake=$model;
		
	}
	public function editAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			//print_r($data);exit();
			try{
				$db_model=new Vehicle_Model_DbTable_DbSubModel();
				 if(isset($data['save_close'])){
					$db_model->updateSubmodel($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
		//	$db = new Vehicle_Model_DbTable_DbMake();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' => 'បន្ថែម​អ្នក​ទទួល​ថ្មី') );
		$this->view->allmake=$model;
		$model=$db->getAllModels();
		$this->view->allmodel=$model;
		$id=$this->getRequest()->getParam('id');
		$sub_model=new Vehicle_Model_DbTable_DbSubModel();
		$row=$sub_model->getSubModelById($id);
		$this->view->row=$row;
	}
    function getModelAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Vehicle_Model_DbTable_DbModel();
    		$makes = $db->getAllModelById($data['make_id']);
    		array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($makes));
    		exit();
    	}
    }
    function getModelsearchAction(){//for search on vehicle index
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Vehicle_Model_DbTable_DbModel();
    		$makes = $db->getAllModelById($data['make_id']);
    		print_r(Zend_Json::encode($makes));
    		exit();
    	}
    }
}

