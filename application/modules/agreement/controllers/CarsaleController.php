<?php
class Agreement_carsaleController extends Zend_Controller_Action {
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
			$db =new agreement_Model_DbTable_Carsale();
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
				
			$rs_rows= $db->getAllCarSaleAgreement($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Agreement Code","Year","Make","Model","Sub Model","Plaque No","Owner Name","Contac","Buyer","Contact","Price in US$","Balance");
			$link=array(
					'module'=>'agreement','controller'=>'carsale','action'=>'edit',
			);
				
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('id'=>$link,'o_name'=>$link,'name'=>$link,'ag_code'=>$link));
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
		$db = new agreement_Model_DbTable_Carsale();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			
			try{
				$id = $db->add($data);
// 				if(@!empty($data['check_invoice'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale/saleform/id/'.$id);
// 				}else{
// // // 					print_r($data);echo "ewrew";exit();
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale');
// 				}
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$rs_customer = $db->getIdNamecustomer();
		$this->view->rs_customer = $rs_customer;
		
		$rs_vechicle = $db->getVehiclerefNo();
		$this->view->rs_vechicle = $rs_vechicle;
		
		$this->view->agreement_code = $db->getNewAgreementCode();
		
	}
	public function editAction(){
		$db =new agreement_Model_DbTable_Carsale();
		$ids = $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			
			try{
				$db->edit($data, $ids);
// 				if(@!empty($data['check_invoice'])){
// 					print_r($data);exit();
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale/saleform/id/'.$ids);
// 				}else{
// // // 					print_r($data);echo "ewrew";exit();
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale');
// 				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		if(empty($ids)){
			$this->_redirect("/agreement");
		}
		$this->view->row_agreement = $db->getSaleAgreementById($ids);
		//print_r($db->getAgreementById($id));
		$rs_customer = $db->getIdNamecustomer();
		$this->view->rs_customer = $rs_customer;
		
		$rs_vechicle = $db->getVehiclerefNo();
		$this->view->rs_vechicle = $rs_vechicle;
	}
	function invoiceAction(){
		$id = $this->getRequest()->getParam('id');
		$db = new agreement_Model_DbTable_Carsale();
		$rs = $db->getSaleAgreementById($id);
		$this->view->rs = $rs;
	}
	function addrecieptAction(){
		$db = new agreement_Model_DbTable_Carsale();
		$id = $this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$id = $db->addReceipt($_data);
			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale/reciept/id/'.$id);
		}
		$this->view->id = $id;
		$this->view->reciept_no = $db->getNewRecieptCode();
		$this->view->sale_no = $db->getInvoiceNo(1);
	}
	function editrecieptAction(){
		$ids = $this->getRequest()->getParam('id');
		$db = new agreement_Model_DbTable_Carsale();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$id = $db->editReceipt($_data,$ids);
			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL . '/carsale/reciept/id/'.$id);
		}
		$this->view->rs = $db->getRecieptById($ids);
		$this->view->sale_no = $db->getInvoiceNo();
	}
	function getinvoiceAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new agreement_Model_DbTable_Carsale();
			$data = $db->getInvoiceById($_data["reffer"]);
			print_r(Zend_Json::encode($data));
			exit();
		}
	}
	function recieptAction(){
		$id = $this->getRequest()->getParam('id');
		$db = new agreement_Model_DbTable_Carsale();
		$rs = $db->getRecieptById($id);
		$this->view->rs = $rs;
	}
	function recieptkhAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new agreement_Model_DbTable_DbAgreement();
			$id = $_dbmodel->getVehiclByBookingId($_data['booking_id']);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	public function saleformAction(){
		$id = $this->getRequest()->getParam('id');
		$db = new agreement_Model_DbTable_Carsale();
		$rs = $db->getSaleAgreementById($id);
		$this->view->rs = $rs;
	}
	public function saleformkhAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new agreement_Model_DbTable_DbAgreement();
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$ids = $post["owner_name"];
			$db = new Application_Model_DbTable_DbGlobal();
			$row=$db->getOwnerById($ids);
			print_r(Zend_Json::encode($row));
			exit();
		}
		
		$row = $db->getAgreeMent($id);
		$this->view->rs = $row;
	}
	public function vehiclesalesreportAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new agreement_Model_DbTable_DbAgreement();
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$ids = $post["owner_name"];
			$db = new Application_Model_DbTable_DbGlobal();
			$row= $db->getOwnerById($ids);
			print_r(Zend_Json::encode($row));
			exit();
		}
	
		$row = $db->getAgreeMent($id);
		$this->view->rs = $row;
	}
	public function generateBarcodeAction(){
			$product_code = $this->getRequest()->getParam('product_code');
			header('Content-type: image/png');
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$barcodeOptions = array('text' => "$product_code",'barHeight' => 20);
			$rendererOptions = array();
			$renderer = Zend_Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions)->render();
	
	}
	
	function getcustomerAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$ids = $post["cu_id"];
			$db = new agreement_Model_DbTable_Carsale();
			$row=$db->getCustomerById($ids);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getvehicleAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$ids = $post["reffer"];
			$db = new agreement_Model_DbTable_Carsale();
			$row=$db->getVehicleById($ids);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}

