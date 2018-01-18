<?php
class Vehicle_engineController extends Zend_Controller_Action {
	const REDIRECT_URL_ADD ='/vehicle/engine/add';
	const REDIRECT_URL_CLOSE ='/vehicle/engine';
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_model = new Vehicle_Model_DbTable_DbEngine();
		$rows=$db_model->getAllEngince();
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Capacity","STATUS");
			$link=array(
					'vehicle'=>'vehicle','controller'=>'engine','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('capacity'=>$link,'status'=>$link));
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
				$db_model=new Vehicle_Model_DbTable_DbEngine();
				if(isset($data['save_new'])){
					$db_model->addEngine($data);
					$this->_redirect(self::REDIRECT_URL_ADD);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}
				else if(isset($data['save_close'])){
					$db_model->addEngine($data);
					$this->_redirect(self::REDIRECT_URL_CLOSE);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
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
	
	}
	public function editAction(){
	
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			//print_r($data);exit();
			try{
				$db_model=new Vehicle_Model_DbTable_DbEngine();
// 				 if(isset($data['save_close'])){
					$db_model->updateEngince($data);
					$this->_redirect(self::REDIRECT_URL_CLOSE);
// 					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_CLOSE);
// 				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id=$this->getRequest()->getParam('id');
		$db_model=new Vehicle_Model_DbTable_DbEngine();
		$row=$db_model->getEnginceById($id);
		//print_r($row);exit();
		$this->view->row=$row;
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
	}
	public function addengineAction(){
		if($this->getRequest()->isPost()){
			$db = new Vehicle_Model_DbTable_DbEngine();
			$data = $this->getRequest()->getPost();
			$id = $db->addEngineAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

