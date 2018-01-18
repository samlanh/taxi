<?php
class Group_OwnervehicleController extends Zend_Controller_Action {
	const REDIRECT_URL='/group/ownervehicle';
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
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
			$db = new Group_Model_DbTable_DbOwner();
// 			if($this->getRequest()->isPost()){
// 				$search=$this->getRequest()->getPost();
// 			}
// 			else{
// 				$search = array(
// 						'adv_search' => '',
// 						'search_status' => -1);
// 			}
			$rs_rows= $db->getAllOwner();
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("Owner Name","Position","ID Card","Phone","EMAIL","Insurance Hotline","Status");
			$link=array(
					'module'=>'group','controller'=>'ownervehicle','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('hand_phone'=>$link,'owner_name'=>$link,'position'=>$link,'id_card'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
	}
    function addAction(){
   	if($this->getRequest()->isPost()){
   		try{
   			$_data = $this->getRequest()->getPost();
   			$db = new Group_Model_DbTable_DbOwner();
   			if(!empty($_data['save_new'])){
   				$db->addOwner($_data);
   				$this->_redirect(self::REDIRECT_URL."/add");
//    				Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
   			}else if(!empty($_data['save_close'])){
   				$db->addOwner($_data);
   				$this->_redirect(self::REDIRECT_URL);
//    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL);
   			}
   		}catch(Exception $e){
   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
   			$err =$e->getMessage();
   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
   		}
   	}
   }
   function editAction(){
	   	if($this->getRequest()->isPost()){
	   		try{
	   			$_data = $this->getRequest()->getPost();
	   			$db = new Group_Model_DbTable_DbOwner();
	   		 if(!empty($_data['save_close'])){
	   				$db->updateOwner($_data);
	   				$this->_redirect(self::REDIRECT_URL);
// 	   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/ownervehicle/index');
	   			}
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   $id=$this->getRequest()->getParam('id');
	   $db=new Group_Model_DbTable_DbOwner();
	   $row=$db->getOwnerById($id);
	   $this->view->row_owner=$row;
   }
}

