<?php
class Stuff_indexController extends Zend_Controller_Action {
	const REDIRECT_URL='/stuff/index';
	const REDIRECT_URL_ADD='/stuff/index/add';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search= array(
						'title'=>'',
						'status_search'=>-1
						);
			}
				$db = new Stuff_Model_DbTable_DbStuff();
				$rows= $db->getAllStuff($search);
				$glClass = new Application_Model_GlobalClass();
				$rows = $glClass->getImgActive($rows, BASE_URL, true);
				$list = new Application_Form_Frmtable();
				$collumns = array("Equipment Name","Reference No"," Year of Product","Make/Model","Serial No.","Telephone Number","Status");
				$link=array(
						'module'=>'stuff','controller'=>'index','action'=>'edit',
				);
				$this->view->list= $list->getCheckList(0, $collumns, $rows,array('equipment_name'=>$link,'reference_no'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$frm = new Stuff_Form_FrmSearch();
		$fm = $frm->FrmSearch();
		Application_Model_Decorator::removeAllDecorator($fm);
		$this->view->frm_search = $fm;
			
	}
	function addAction(){
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Stuff_Model_DbTable_DbStuff();
			try {
				if(!empty($_data['save_new'])){
				   $_dbmodel->addStuff($_data);
				   $this->_redirect(self::REDIRECT_URL_ADD);
// 					Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}else if(!empty($_data['save_close'])){
				    $_dbmodel->addStuff($_data);
				    $this->_redirect(self::REDIRECT_URL);
// 					Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllPackageDayOption();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
	function editAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Stuff_Model_DbTable_DbStuff();
			try {
				 if(!empty($data['save_close'])){
					$db->updateStuff($data);
					$this->_redirect(self::REDIRECT_URL);
// 					Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllPackageDayOption();
		$id=$this->getRequest()->getParam('id');
		$db_stuff= new Stuff_Model_DbTable_DbStuff();
		$row_stuff=$db_stuff->getStuffById($id);
	    $rows_stuf_de=$db_stuff->getStuffDetailById($id);
	    //print_r($rows_stuf_de);exit();
	    $this->view->row_stuff=$row_stuff;
	    $this->view->rows_detail=$rows_stuf_de;
	    
	    $db = new Application_Model_DbTable_DbGlobal();
	    $status=$db->getViews(2);
	    $this->view->status_view=$status;
	}
}

