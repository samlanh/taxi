<?php
class Agreement_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	const REDIRECT_URL='/agreement';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db =new agreement_Model_DbTable_DbAgreement();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'title' => $formdata['title'],
						'status_search'=>$formdata['status_search'],
				);
			}
			else{
				$search = array(
						'title' => '',
						'status_search' => -1,
				);
			}
				
			$rs_rows= $db->getAllAgreement($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Agreement Code","Owner Name","Customer Name","Booking Id","Vehicle Reffer","Inception Date","Return Date","Return Time","Period","Price/Day");
			$link=array(
					'module'=>'agreement','controller'=>'index','action'=>'edit',
			);
				
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('agreement_code'=>$link,'owner_name'=>$link,'customer_name'=>$link,'booking_id'=>$link,'reffer'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
// 		$frm = new Location_Form_FrmSearch();
// 		$frm =$frm->search();
// 		Application_Model_Decorator::removeAllDecorator($frm);
// 		$this->view->frm_search = $frm;
	}
	public function addAction(){
		$dbagree =new agreement_Model_DbTable_DbAgreement();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$dbagree->addAgreement($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/index/add');
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/index');
				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$owner=$db->getAllNameOwner();
		$this->view->country = $db->getAllCountry();
		$this->view->rows_owner=$owner;
		$cus=new Booking_Model_DbTable_DbBooking();
		$rows_cus=$cus->getIdNamecustomer();
		$this->view->rows_cus=$rows_cus;
		$row_vehicle=$cus->getVehiclerefNo();
		$this->view->row_vehicle=$row_vehicle;
		$db = new agreement_Model_DbTable_DbAgreement();
		$this->view->rsbooking = $db ->getAllBookingNumber();
		
		$this->view->agreement_ref=$dbagree->getNewAgreementCode();
		$prefixed = $dbagree->getSystemSetting('agreement_code');
		$this->view->prefixed = $prefixed['value'];
		$this->view->lastagreement = $dbagree->getLastAgreementCode();
		
	}
	public function editAction(){
		$db =new agreement_Model_DbTable_DbAgreement();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$db->addAgreement($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", "/agreement/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam('id');
		if(empty($id)){
			$this->_redirect("/agreement");
		}
		$this->view->row_agreement = $db->getAgreementById($id);
		//print_r($db->getAgreementById($id));
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$owner=$db->getAllNameOwner();
		$this->view->country = $db->getAllCountry();
		$this->view->rows_owner=$owner;
		$cus=new Booking_Model_DbTable_DbBooking();
		$rows_cus=$cus->getIdNamecustomer();
		$this->view->rows_cus=$rows_cus;
		$row_vehicle=$cus->getVehiclerefNo();
		$this->view->row_vehicle=$row_vehicle;
		$db = new agreement_Model_DbTable_DbAgreement();
		$this->view->rsbooking = $db ->getAllBookingNumber();
		
		$dbagree =new agreement_Model_DbTable_DbAgreement();
		$this->view->agreement_ref=$dbagree->getNewAgreementCode();
		$prefixed = $dbagree->getSystemSetting('agreement_code');
		$this->view->prefixed = $prefixed['value'];
		$this->view->lastagreement = $dbagree->getLastAgreementCode();
	}
	function getBookingAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new agreement_Model_DbTable_DbAgreement();
			$data = $_dbmodel->getBookingInfoById($_data);
			print_r(Zend_Json::encode($data));
			exit();
		}
	}
	function getVehiclebookingAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new agreement_Model_DbTable_DbAgreement();
			$id = $_dbmodel->getVehiclByBookingId($_data['booking_id']);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	public function getRowOwnerAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$ids = $post["owner_name"];
			$db = new agreement_Model_DbTable_DbAgreement();
			$row=$db->getOwnerById($ids);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

