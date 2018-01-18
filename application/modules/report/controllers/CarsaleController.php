<?php
class Report_CarsaleController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
    function indexAction(){
	  	
	 }
	 function rptSaleAgreementAction(){//release all loan
	 	if($this->getRequest()->isPost()){
	 		$search = $this->getRequest()->getPost();
	 	}
	 	else{
	 		$search = array(
	 				'adv_search'=>'',
	 				'status'=>'',
	 				'start_date'=> date('Y-m-d'),
	 				'end_date'=>date('Y-m-d'));
	 	}
	 	$this->view->data = $search;
	 	
	 	$db = new Report_Model_DbTable_DbSale();
	 	$rs_rows = $db->getAllSaleAgreement($search);
	 	$this->view->row = $rs_rows;
	 	
	 	$frm = new Application_Form_FrmAdvanceSearch();
	 	$form = $frm->AdvanceSearch();
	 	Application_Model_Decorator::removeAllDecorator($form);
	 	$this->view->frm = $form;
	 	
	 	$list = new Application_Form_Frmtable();
	 	$collumns = array("Agreement Code","Year","Make","Model","Sub Model","Plaque No","Owner Name","Contac","Buyer","Contact","Price in US$","Balance","Date");
	 	$link=array(
	 			'module'=>'agreement','controller'=>'carsale','action'=>'saleform',
	 	);
	 	
	 	$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('id'=>$link,'o_name'=>$link,'name'=>$link,'ag_code'=>$link),'_blank');
	 }
	function rptSaleInvoiceAction(){//release all loan
	if($this->getRequest()->isPost()){
	 		$search = $this->getRequest()->getPost();
	 	}
	 	else{
	 		$search = array(
	 				'adv_search'=>'',
	 				'status'=>'',
	 				'start_date'=> date('Y-m-d'),
	 				'end_date'=>date('Y-m-d'));
	 	}
	 	$this->view->data = $search;
	 	
	 	$db = new Report_Model_DbTable_DbSale();
	 	$rs_rows = $db->getAllSaleAgreement($search);
	 	$this->view->row = $rs_rows;
	 	
	 	$frm = new Application_Form_FrmAdvanceSearch();
	 	$form = $frm->AdvanceSearch();
	 	Application_Model_Decorator::removeAllDecorator($form);
	 	$this->view->frm = $form;
	 	
	 	$list = new Application_Form_Frmtable();
	 	$collumns = array("Agreement Code","Year","Make","Model","Sub Model","Plaque No","Owner Name","Contac","Buyer","Contact","Price in US$","Balance","Date");
	 	$link=array(
	 			'module'=>'agreement','controller'=>'carsale','action'=>'invoice',
	 	);
	 	
	 	$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('id'=>$link,'o_name'=>$link,'name'=>$link,'ag_code'=>$link),'_blank');
	 }
	 
	  function rptSaleRecieptAction(){
	  if($this->getRequest()->isPost()){
	 		$search = $this->getRequest()->getPost();
	 	}
	 	else{
	 		$search = array(
	 				'adv_search'=>'',
	 				'status'=>'',
	 				'start_date'=> date('Y-m-d'),
	 				'end_date'=>date('Y-m-d'));
	 	}
	 	$this->view->data = $search;
	 	
	 	$db = new Report_Model_DbTable_DbSale();
	 	$rs_rows = $db->getAllSaleReciept($search);
	 	$this->view->row = $rs_rows;
	 	
	 	$frm = new Application_Form_FrmAdvanceSearch();
	 	$form = $frm->AdvanceSearch();
	 	Application_Model_Decorator::removeAllDecorator($form);
	 	$this->view->frm = $form;
	 	$target = 'target="_blank"';
	 	$list = new Application_Form_Frmtable();
	 	$collumns = array("Agreement Code","Reciept Code","Year","Make","Model","Sub Model","Plaque No","Owner Name","Contac","Buyer","Contact","Price in US$","Amount Paid","Balance","Date");
	 	$link=array(
	 			'module'=>'agreement','controller'=>'carsale','action'=>'reciept',
	 	);
	 	
	 	$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('id'=>$link,'o_name'=>$link,'name'=>$link,'ag_code'=>$link),'_blank');
	 }
	  
  
}

