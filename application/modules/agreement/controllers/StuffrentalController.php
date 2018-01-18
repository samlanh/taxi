<?php
class Agreement_StuffrentalController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db =new agreement_Model_DbTable_DbProductAgreement();
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
			$collumns = array("Agreement Code","Owner Name","Customer Name","Booking Id","Start Date","Finish Date","Period","Rental Fee","Refundable Deposit","Grand Total","
					Paid Amount","Due Amount");
			$link=array(
					'module'=>'agreement','controller'=>'stuffrental','action'=>'edit',
			);
		
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('agreement_code'=>$link,'owner_name'=>$link,'customer_name'=>$link,'booking_id'=>$link,'reffer'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function iconAction(){
		
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db =new agreement_Model_DbTable_DbProductAgreement();
			try{
				$db->addProductAgreement($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/agreement/stuffrental/add");
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
		
		$dbagree =new agreement_Model_DbTable_DbAgreement();
		$this->view->agreement_ref=$dbagree->getNewAgreementCode();
		$prefixed = $dbagree->getSystemSetting('agreement_code');
		$this->view->prefixed = $prefixed['value'];
		$this->view->lastagreement = $dbagree->getLastAgreementCode();
	}
	public function editAction(){
		$id = $this->getRequest()->getParam('id');
		$dbagreement =new agreement_Model_DbTable_DbProductAgreement();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			
			try{
				$dbagreement->addProductAgreement($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/agreement/stuffrental/add");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$this->view->rs = $dbagreement->getAgreementById($id);
		
		
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
	function getproductAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new agreement_Model_DbTable_DbProductAgreement();
			$data = $_dbmodel->getProductBooking($_data['booking_id']);
			print_r(Zend_Json::encode($data));
			exit();
		}
	}
}

