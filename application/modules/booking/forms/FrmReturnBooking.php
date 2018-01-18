<?php 
Class Booking_Form_FrmReturnBooking extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate =null;//text validate
	protected $filter=null;
	protected $t_num=null;
	protected $text=null;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function FrmReturnBooking($data=null){
		$db = new Booking_Model_DbTable_DbReturnBook();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>$this->tvalidate,
				'onkeyup'=>'this.submit()',
				'placeholder'=>$this->tr->translate("SEARCH_BRANCH_INFO")
		));
		$_title->setValue($request->getParam("adv_search"));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>$this->filter));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
				'value'=>'Search ',
		
		));
		$c_date = date("Y-m-d");
		$opt_book_no = array(
				-1=>$this->tr->translate("CHOOSE"));
		$row_book_no = $db->getBookingNo();
		foreach ($row_book_no as $rs_book_no){
			$opt_book_no[$rs_book_no["id"]] = $rs_book_no["booking_no"];
		}
		$booking_no = new Zend_Dojo_Form_Element_FilteringSelect("book_no");
		$booking_no->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
		));
		$booking_no->setMultiOptions($opt_book_no);
		
		$date_book = new Zend_Dojo_Form_Element_DateTextBox("date_book");
		$date_book->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
		));
		$date_book->setValue($c_date);
		
		$picku_date = new Zend_Dojo_Form_Element_DateTextBox("pickup_date");
		$picku_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
		));
		$picku_date->setValue($c_date);
		
		
		$return_date = new Zend_Dojo_Form_Element_DateTextBox("return_date");
		$return_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
		));
		$return_date->setValue($c_date);
		
		
		
		$_id = new Zend_Form_Element_Hidden('id');
		if(!empty($data)){
			
		}
		
		$this->addElements(array($_title,$booking_no,$date_book,$picku_date,$return_date,$_btn_search));
		
		return $this;
		
	}
	
}