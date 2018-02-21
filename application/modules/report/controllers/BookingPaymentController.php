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
	 
// 	  function rptDriverPaymentAction(){
// 	  	if($this->getRequest()->isPost()){
// 	  		$search = $this->getRequest()->getPost();
// 	  	}
// 	  	else{
// 	  		$search = array(
// 	  				'adv_search'=>'',
// 	  				'status'=>-1,
// 	  				'start_date'=> date('Y-m-d'),
// 	  				'end_date'=>date('Y-m-d'));
// 	  	}
	  	
// 	  	$db = new Report_Model_DbTable_DbBookingPayment();
// 		$this->view->rows = $db->getAllVehiclePrice($search);
		
// 		$frm = new Application_Form_FrmAdvanceSearch();
// 		$form = $frm->AdvanceSearch();
// 		Application_Model_Decorator::removeAllDecorator($form);
// 		$this->view->frm = $form;
// 	  }
	  
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
	  	$this->view->rows = $db->getAllExpenseMaintenance($search);
	  
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
	  
	  function rptVehiclemaintenanceDetailAction(){
	  	$id=$this->getRequest()->getParam('id');
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->driver= $db->getDriverInfor($id);
	  	$this->view->car = $db->getCarInfor($id);
	  	$this->view->he_m_detail = $db->getVehicleMaintenantDetail($id);
	  }
	  
	  function rptExpenseDetailAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'title'			=>'',
	  				'payment_method'=>-1,
	  				'start_date'	=> date('Y-m-d'),
	  				'end_date'		=>date('Y-m-d'),
	  				'status_search' =>-1
	  				);
	  	}
	  	 
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->rows = $db->getAllExpenseDetail($search);
	  	 
	  	$frm = new Expense_Form_FrmSearchInfo();
	  	$frm =$frm->search();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function rptDriverPaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'=>-1,
// 	  				'start_date'=> date('Y-m-d'),
// 	  				'end_date'=>date('Y-m-d')
	  		);
	  	}
	  
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->d_payment = $db->getAllDriverPyment($search);
	  
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
	  
	  function rptCommissionpaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'=>-1,
	  				// 	  				'start_date'=> date('Y-m-d'),
	  		// 	  				'end_date'=>date('Y-m-d')
	  		);
	  	}
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row=$this->view->d_payment = $db->getAllCommission($search);
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
	  
	  function rptCommissionpaymentdetailAction(){
	  	$id = $this->getRequest()->getParam("id");
	  	if (empty($id)){
	  		$this->_redirect("/report/bookingpayment/rpt-commissionpayment");
	  	}
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row= $db->getCommissionPaymentById($id);
	  	if (empty($row)){
	  		$this->_redirect("/report/bookingpayment/rpt-commissionpayment");
	  	}
	  	$this->view->commision_payment = $row;
	  	$this->view->commision_detail = $db->getCommissionPaymentDetail($id);
	  }
	  
	  function rptCustomerNealyPaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'=>-1,
	  				'customer'=>0,
	  				'status'=>0,
	  				'end_date'=>date('Y-m-d')
	  		);
	  	}
	  	$this->view->search=$search;
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row=$this->view->row_customer = $db->getCustomerNearlyPayment($search);
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
  
}

