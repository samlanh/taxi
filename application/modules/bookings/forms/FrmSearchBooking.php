<?php
class Bookings_Form_FrmSearchBooking extends Zend_Dojo_Form{
	protected $tr = null;
	protected $btn =null;//text validate
	protected $filter = null;
	protected $text =null;
	protected $validate = null;
	protected $date;
	protected $textarea=null;
	protected $number;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->btn = 'dijit.form.Button';
		$this->validate = 'dijit.form.ValidationTextBox';
		$this->date = 'dijit.form.DateTextBox';
		$this->textarea = 'dijit.form.SimpleTextarea';
		$this->number = 'dijit.form.NumberTextBox';
	}
	public function FormSearch(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$c_date = date("Y-m-d");
		$from_book_date = new Zend_Dojo_Form_Element_DateTextBox("from_book_date");
		$from_book_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",
				));
		if($request->getParam("from_book_date")==""){
			$from_book_date->setValue($c_date);
		}else{
			$from_book_date->setValue($request->getParam("from_book_date"));
		}
		
		$to_book_date = new Zend_Dojo_Form_Element_DateTextBox("to_book_date");
		$to_book_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",
		));
		if($request->getParam("to_book_date")==""){
			$to_book_date->setValue($c_date);
		}else{
			$to_book_date->setValue($request->getParam("to_book_date"));
		}
		
		$search_tex = new Zend_Dojo_Form_Element_TextBox("search_text");
		$search_tex->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		
		$db_booking = new Booking_Model_DbTable_DbBooking();
		$row_cu = $db_booking->getIdNamecustomer();
		$opt_cu = array('-1'=>$this->tr->translate("CHOOSE_CUSTOMER"));
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["first_name"]." ".$rs["last_name"];
		}
		$customer->setMultiOptions($opt_cu);
		$customer->setValue($request->getParam("customer"));
		$this->addElements(array($from_book_date,$to_book_date,$search_tex,$customer));
		return $this;
	}
	
	
}

