<?php
class Group_CustomercarrentalController extends Zend_Controller_Action {
	const REDIRECT_URL_ADD ='/group/customercarrental/add';
	const REDIRECT_URL_ADD_CLOSE ='/group/customercarrental/';
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
	    $db_make = new Group_Model_DbTable_DbCustomer();
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
				);
			}
			$rows=$db_make->getAllCustomer($search);
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgActive($rows, BASE_URL, true);
			
			$list = new Application_Form_Frmtable();
			$collumns = array("Customer No","Lessee Name","Nationality","Phone Number","IDCard/Passport","Address","User Name","Status");
			$link=array(
					'module'=>'group','controller'=>'customercarrental','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('cus_no'=>$link,'customer'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm =$frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	
	function addAction(){
		if($this->getRequest()->isPost()){
			$data= $this->getRequest()->getPost();
			try {
			    $db = new Group_Model_DbTable_DbCustomer();
			    $db->addCustomer($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}else{
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
				}
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews();
		$this->view->status_view=$status;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->cus_code=$db->getcustomerno();
	}
	
	function editAction(){
	    $id=$this->getRequest()->getParam('id');
	    $db = new Group_Model_DbTable_DbCustomer();
	    if($this->getRequest()->isPost()){
	        $data= $this->getRequest()->getPost();
	        $data['id']=$id;
	        try {
	            $db->editCustomer($data);
	            if(!empty($data['save_new'])){
	                Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
	            }else{
	                Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
	            }
	            Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
	        }catch (Exception $e) {
	            Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
	            $err =$e->getMessage();
	            Application_Model_DbTable_DbUserLog::writeMessageError($err);
	        }
	    }
	    $this->view->row=$db->getCustomerById($id);
	    $db = new Application_Model_DbTable_DbGlobal();
	    $status=$db->getViews();
	    $this->view->status_view=$status;
	    $db = new Application_Model_DbTable_DbGlobal();
	    $this->view->cus_code=$db->getcustomerno();
	}
	function addvehiceltypeAction(){
		if($this->getRequest()->isPost()){
		    $db = new Group_Model_DbTable_DbCustomer();
			$data = $this->getRequest()->getPost();
			$id = $db->addVehicleTypeAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
}

