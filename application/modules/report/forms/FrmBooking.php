<?php 
Class Report_Form_FrmBooking extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate ;//text validate
	protected $filter;
	protected $text;
	protected $date;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->date = 'dijit.form.DateTextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function FrmBooking(){
		
		$advance_search = new Zend_Dojo_Form_Element_DateTextBox("adv_search");
		$advance_search->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',));
		
		$db = new Report_Model_DbTable_booking();
		$row_book_no = $db->getBookNo();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$c_date = date("Y-m-d");
		
		$opt_book_no = array('-1'=>$this->tr->translate("Select Booking Number"));
		foreach ($row_book_no as $rs){
			$opt_book_no[$rs["booking_no"]] = $rs["booking_no"];
		}
		$book_no = new Zend_Dojo_Form_Element_FilteringSelect("book_no");
		$book_no->setMultiOptions($opt_book_no);
		$book_no->setAttribs(array('dojoType'=>"dijit.form.FilteringSelect",'class'=>'fullside',));
		$book_no->setValue($request->getParam("book_no"));
		$this->addElement($book_no);
		
		$opt_customer = array('-1'=>$this->tr->translate("Select Customer"));
		foreach ($db->getCustomerNo() as $rs){
			$opt_customer[$rs["id"]] = $rs["customer"];
		}
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside','placeholder'=>$this->tr->translate("CUSTOMER_NAME")));
		$customer->setMultiOptions($opt_customer);
		$customer->setValue($request->getParam("customer"));
		
		$date_book = new Zend_Dojo_Form_Element_DateTextBox("date_book");
		$date_book->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',));
		if($request->getParam("date_book")==""){
			$date_book->setValue($c_date);
		}else{
			$date_book->setValue($request->getParam("date_book"));
		}
		
		$pickup_date = new Zend_Dojo_Form_Element_DateTextBox("pickup_date");
		$pickup_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',));
		if($request->getParam("pickup_date")==""){
			$pickup_date->setValue($c_date);
		}else{
			$pickup_date->setValue($request->getParam("pickup_date"));
		}
		
		$return_date = new Zend_Dojo_Form_Element_DateTextBox("return_date");
		$return_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',));
		if($request->getParam("return_date")==""){
			$return_date->setValue($c_date);
		}else{
			$return_date->setValue($request->getParam("return_date"));
		}
		
		$opt_status = array('-1'=>"Select Status",'1'=>"Active",'0'=>"Deactive"); 
		$status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$status->setMultiOptions($opt_status);
		$status->setValue($request->getParam("status"));
		
		$this->addElements(array($advance_search,$book_no,$customer,$pickup_date,$return_date,$date_book,$status));
		return $this;
		
	}
	
}