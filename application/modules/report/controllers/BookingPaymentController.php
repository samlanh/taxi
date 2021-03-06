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
	  
	  function rptVehiclemaintenanceAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'title'=>'',
	  				'payment_method'=>'payment_method',
	  				'status'=>-1,
	  				'start_date'=> date('Y-m-d'),
	  				'end_date'=>date('Y-m-d'));
	  	}
	  
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->rows = $db->getAllExpenseMaintenance($search);
	  
	  	$frm = new Expense_Form_FrmSearchInfo();
	  	$frm =$frm->search();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
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
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'start_date' => date("Y-m-01"),
						'end_date' => date("Y-m-d"),
						'search_text' => "",
						'driver_search'=>0,
						'status'=>1
				);
		}
	    $this->view->search=$search;
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->d_payment = $db->getAllDriverPyment($search);
	  
	  	$frm = new Bookings_Form_FrmSearchBooking();
		$frm =$frm->FormSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	  }
	  
	  function rptDriverPaymentdetailAction(){
	  	$id = $this->getRequest()->getParam("id");
	  	if (empty($id)){
	  		$this->_redirect("/report/bookingpayment/rpt-commissionpayment");
	  	}
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row= $db->getAllDriverPymentById($id);
	  	if (empty($row)){
	  		$this->_redirect("/report/bookingpayment/rpt-driver-payment");
	  	}
	  	$this->view->commision_payment = $row;
	  	$this->view->commision_detail = $db->getDriverPaymentDetail($id);
	  	
	  	$db_gs = new Application_Model_DbTable_DbGlobalSelect();
	  	$row =array();
	  	$this->view->tel= $db_gs->getWebsiteSetting('tel');
	    $this->view->email= $db_gs->getWebsiteSetting('email');
	  	$this->view->address= $db_gs->getWebsiteSetting('address');
	  	 
	  }
	  
	  function rptCommissionpaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  		        'search_text'=>'',
	  				'agency_search'=>0,
	  		        'status'=>1,
	  				'start_date'=> date('Y-m-01'),
	  				'end_date'=>date('Y-m-d')
	  		);
	  	}
	  	$this->view->search=$search;
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row=$this->view->d_payment = $db->getAllCommission($search);
	  	$frm = new Bookings_Form_FrmSearchBooking();
		$frm =$frm->FormSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
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
	  	$db_gs = new Application_Model_DbTable_DbGlobalSelect();
	  	$row =array();
	  	$this->view->tel= $db_gs->getWebsiteSetting('tel');
	  	$this->view->email= $db_gs->getWebsiteSetting('email');
	  	$this->view->address= $db_gs->getWebsiteSetting('address');
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
	  	$row=$db->getCustomerNearlyPayment($search);
	  	$glClass = new Application_Model_GlobalClass();
	  	$rs_rows=$glClass->getHoursStudy($row);
	  	$row=$this->view->row_customer = $rs_rows;
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
	  
	  function rptCustomerPaymentAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'adv_search'=>'',
	  				'status'    =>-1,
	  				'start_date'=> date('Y-m-d'),
	  			  	'end_date'  =>date('Y-m-d')
	  		);
	  	}
	  	 
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$this->view->d_customer = $db->getAllCustomerPyment($search);
	  	$frm = new Application_Form_FrmAdvanceSearch();
	  	$form = $frm->AdvanceSearch();
	  	Application_Model_Decorator::removeAllDecorator($form);
	  	$this->view->frm = $form;
	  }
	  
	  function rptCustomerPaymentDetailAction(){
	  	$id = $this->getRequest()->getParam("id");
	  	if (empty($id)){
	  		$this->_redirect("/report/bookingpayment/rpt-commissionpayment");
	  	}
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row= $db->getAllCustomerPymentById($id);
	  	if (empty($row)){
	  		$this->_redirect("/report/bookingpayment/rpt-customer-payment");
	  	}
	  	$this->view->customer_payment = $row;
	  	$this->view->customer_payment_detail = $db->getCustomerPaymentDetail($id);
	  }
	 
	function  rptCarbookingAction(){
	  	try{
	  		$db = new Report_Model_DbTable_DbBookingPayment();
	  		if($this->getRequest()->isPost()){
	  			$search=$this->getRequest()->getPost();
	  		}
	  		else{
	  			$search = array(
	  					'to_book_date'   => date("Y-m-d"),
	  					'from_book_date' => date("Y-m-01"),
	  					'search_text'    => "",
	  					'customer'       =>0,
	  					'working_status' =>-1,
	  					'date_type'		 =>'2',
	  					'agency_search'	 =>'0',
	  					'vehicle_type'	 =>'0',
	  					'driver_search'  =>0,
	  					'agency_search'  =>0,
	  					'status'       =>-1,
	  			);
	  		}
	  		$this->view->search=$search;
	  		$rs_rows= $db->getAllCarBooking($search);
	  		$glClass = new Application_Model_GlobalClass();
	  		$rs_rows=$glClass->getHoursStudy($rs_rows);
	  		$this->view->result=$rs_rows;
	  		
	  	}catch (Exception $e){
	  		Application_Form_FrmMessage::message("Application Error");
	  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	  	}
	  	$frm = new Bookings_Form_FrmSearchBooking();
	  	$frm =$frm->FormSearch();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function rptCustomerAlertTimeAction(){
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				    'to_book_date'   => date("Y-m-d"),
	  					'from_book_date' => date("Y-m-01"),
	  					'search_text'    => "",
	  					'customer'       =>0,
	  					'working_status' =>-1,
	  					'date_type'		 =>'2',
	  					'driver_search'  =>0,
	  					'status'       =>1,
	  		);
	  	}
	  	$this->view->search=$search;
	  	$glClass = new Application_Model_GlobalClass();
	  	$db = new Report_Model_DbTable_DbBookingPayment();
	  	$row=$db->getCustomerAlertTime($search);
	  	$row=$glClass->getHoursStudy($row);
	  	$this->view->d_customer = $row;
	  	$frm = new Bookings_Form_FrmSearchBooking();
	  	$frm =$frm->FormSearch();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function rptIncomeDetailAction(){
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
	  	$this->view->rows = $db->getAllIncomeDetail($search);
	  	 
	  	$frm = new Expense_Form_FrmSearchInfo();
	  	$frm =$frm->search();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function  rptCarbookingPaymentAction(){
	  	try{
	  		$db = new Report_Model_DbTable_DbBookingPayment();
	  		if($this->getRequest()->isPost()){
	  			$search=$this->getRequest()->getPost();
	  		}
	  		else{
	  			$search = array(
	  					'to_book_date'   => date("Y-m-d"),
	  					'from_book_date' => date("Y-m-d"),
	  					'search_text'    => "",
	  					'customer'       =>0,
	  					'working_status' =>-1,
	  					'date_type'		 =>'2',
	  					'agency_search'	 =>'0',
	  					'vehicle_type'	 =>'0',
	  					'driver_search'  =>0,
	  					'agency_search'  =>0,
	  					'status'       =>-1,
	  			);
	  		}
	  		$rs_rows= $db->getAllCarBookingPayment($search);
	  		$glClass = new Application_Model_GlobalClass();
	  		$rs_rows=$glClass->getHoursStudy($rs_rows);
	  		$this->view->result=$rs_rows;
	  	  
	  	}catch (Exception $e){
	  		Application_Form_FrmMessage::message("Application Error");
	  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	  	}
	  	$frm = new Bookings_Form_FrmSearchBooking();
	  	$frm =$frm->FormSearch();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function rptBookingClearedAction(){ 
	  	try{
	  		$db = new Bookings_Model_DbTable_DbBookingCleared();
	  		if($this->getRequest()->isPost()){
	  			$search=$this->getRequest()->getPost();
	  		}
	  		else{
	  			$search = array(
	  					'to_book_date'   => date("Y-m-d"),
	  					'from_book_date' => date("Y-m-01"),
	  					'search_text'    => "",
	  					'customer'       =>0,
	  					'working_status' =>-1,
	  					'date_type'		 =>'2',
	  					'agency_search'	 =>'0',
	  					'vehicle_type'	 =>'0',
	  					'driver_search'  =>0,
	  	
	  					'start_time'  =>'',
	  					'delivery_time'  =>'',
	  					'agency_search'  =>0,
	  					'status'       =>1,
	  			);
	  		}
	  		
	  		$rs_rows= $db->getAllBookingClearedPayment($search);
	  		$list = new Application_Form_Frmtable();
	  		$this->view->rows=$rs_rows;
	  		$this->view->search=$search;
	  	}catch (Exception $e){
	  		Application_Form_FrmMessage::message("Application Error");
	  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	  	}
	  	$frm = new Bookings_Form_FrmSearchBooking();
	  	$frm =$frm->FormSearch();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
	  function rptAgencypaidAction(){
	      if($this->getRequest()->isPost()){
	          $search = $this->getRequest()->getPost();
	      }
	      else{
	          $search = array(
	              'search_text'=>'',
	              'agency_search'=>0,
	              'status'=>1,
	              'is_paid'=>1,
	              'start_date'=> date('Y-m-01'),
	              'end_date'=>date('Y-m-d')
	          );
	      }
	      $this->view->search=$search;
	      $db = new Report_Model_DbTable_DbBookingPayment();
	      $row=$this->view->d_payment = $db->getAllAgencyPaid($search);
	      $frm = new Bookings_Form_FrmSearchBooking();
	      $frm =$frm->FormSearch();
	      Application_Model_Decorator::removeAllDecorator($frm);
	      $this->view->frm_search = $frm;
	  }
	  
	  function rptDriverPaidAction(){
	      if($this->getRequest()->isPost()){
	          $search = $this->getRequest()->getPost();
	      }
	      else{
	          $search = array(
	              'search_text'=>'',
	              //'agency_search'=>0,
	              'status'=>1,
	              'is_paid'=>1,
	              'driver_search'=>0,
	              'start_date'=> date('Y-m-01'),
	              'end_date'=>date('Y-m-d')
	          );
	      }
	      $this->view->search=$search;
	      $db = new Report_Model_DbTable_DbBookingPayment();
	      $row=$this->view->d_payment = $db->getAllADriverPaid($search);
	      $frm = new Bookings_Form_FrmSearchBooking();
	      $frm =$frm->FormSearch();
	      Application_Model_Decorator::removeAllDecorator($frm);
	      $this->view->frm_search = $frm;
	  }
	  
	  function rptCarentalPaymentAction(){
	      if($this->getRequest()->isPost()){
	          $search=$this->getRequest()->getPost();
	      }
	      else{
	          $search = array(
	              'start_date'   => date("Y-m-01"),
	              'end_date'     => date("Y-m-d"),
	              'search_text'  => "",
	              'lessee_name'  => 0,
	              'vehicle_type' => -1,
	              'plate_number' => 0,
	              'is_return'	   => -1,
	              'status'       => -1,
	          );
	      }
	      $this->view->search=$search;
	      $db = new Report_Model_DbTable_DbBookingPayment();
	      $this->view->rs_carental= $db->getAllCarentalPyment($search);
	      $frm = new Bookings_Form_FrmSearchBooking();
	      $frm =$frm->FormSearch($search);
	      Application_Model_Decorator::removeAllDecorator($frm);
	      $this->view->frm_search = $frm;
	  }
	  
	  function rptCarentalPaymentdetailAction(){
	      $id = $this->getRequest()->getParam("id");
	      if (empty($id)){
	          $this->_redirect("/report/bookingpayment/rpt-carental-payment");
	      }
	      $db = new Report_Model_DbTable_DbBookingPayment();
	      $this->view->rs_carental= $db->getCarentalById($id);
	      $this->view->rows=$db->getCarrentalDetail($id);
	  }
	  
	  
	  function  rptBookingIncomeAction(){
	  	try{
	  		$db = new Bookings_Model_DbTable_DbBookingCleared();
	  		if($this->getRequest()->isPost()){
	  			$search=$this->getRequest()->getPost();
	  		}
	  		else{
	  			$search = array(
	  					'to_book_date'   => date("Y-m-d"),
	  					'from_book_date' => date("Y-m-01"),
	  					'search_text'    => "",
	  					'customer'       =>0,
	  					'working_status' =>-1,
	  					'date_type'		 =>'2',
	  					'agency_search'	 =>'0',
	  					'vehicle_type'	 =>'0',
	  					'driver_search'  =>0,
	  
	  					'start_time'  =>'',
	  					'delivery_time'  =>'',
	  					'agency_search'  =>0,
	  					'status'       =>1,
	  			);
	  		}
	  	  
	  		$rs_rows= $db->getAllIncomeBooking($search);
	  		$list = new Application_Form_Frmtable();
	  		$this->view->rows=$rs_rows;
	  		$this->view->search=$search;
	  	}catch (Exception $e){
	  		Application_Form_FrmMessage::message("Application Error");
	  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	  	}
	  	$frm = new Bookings_Form_FrmSearchBooking();
	  	$frm =$frm->FormSearch();
	  	Application_Model_Decorator::removeAllDecorator($frm);
	  	$this->view->frm_search = $frm;
	  }
	  
}

