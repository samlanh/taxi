<?php
class Report_BookingPaymentController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
    function indexAction(){
	  	
	 }
	 
	  function rptDriverPaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'=>-1,
	  				'start_date'=> date('Y-m-d'),
	  				'end_date'=>date('Y-m-d'));
	  	}
	  	
	  	$db = new Report_Model_DbTable_DbBookingPayment();
		$this->view->rows = $db->getAllVehiclePrice($search);
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$form = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm = $form;
	  }
	  
	  function rptVehiclemaintenanceAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'=>-1,
	  				'start_date'=> date('Y-m-d'),
	  				'end_date'=>date('Y-m-d'));
	  	}
	  
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->rows = $db->getAllVehiclePrice($search);
	  
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
  
}

