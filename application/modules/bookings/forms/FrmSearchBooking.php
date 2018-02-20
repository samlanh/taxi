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
		
		$row_cu = $_db->getAllCustomers();
		$opt_cu = array(0=>$this->tr->translate("SELECT_CUSTOMER"));
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCustomer();'));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["name"];
		}
		$customer->setMultiOptions($opt_cu);
		$customer->setValue($request->getParam("customer"));
		
		$row_dri = $_db->getAllDriver();
		$opt_dri = array(0=>$this->tr->translate("SELECT_DRIVER"));
		$driver_search = new Zend_Dojo_Form_Element_FilteringSelect("driver_search");
		$driver_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_dri as $rs){
			$opt_dri[$rs["id"]] = $rs["name"];
		}
		$driver_search->setMultiOptions($opt_dri);
		$driver_search->setValue($request->getParam("driver_search"));
		
		$row_veh = $_db->getVehicleHasDriver();
		$opt_vehi = array(0=>$this->tr->translate("SELECT_VEHICLE"));
		$vehicle_search = new Zend_Dojo_Form_Element_FilteringSelect("vehicle_search");
		$vehicle_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_veh as $rs){
			$opt_vehi[$rs["id"]] = $rs["name"];
		}
		$vehicle_search->setMultiOptions($opt_vehi);
		$vehicle_search->setValue($request->getParam("vehicle_search"));
		
		$row_agen = $_db->getAllAgency();
		$opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"));
		$agency_search = new Zend_Dojo_Form_Element_FilteringSelect("agency_search");
		$agency_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency_search->setMultiOptions($opt_agen);
		$agency_search->setValue($request->getParam("agency_search"));
		
		$row_payment = $_db->getVewOptoinTypeByTypes(11);
		$opt_payment = array(0=>$this->tr->translate("SELECT_PAYMENT_METHOD"));
		$payment_method_search = new Zend_Dojo_Form_Element_FilteringSelect("payment_method_search");
		$payment_method_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_payment as $rs){
			$opt_payment[$rs["id"]] = $rs["name"];
		}
		$payment_method_search->setMultiOptions($opt_payment);
		$payment_method_search->setValue($request->getParam("payment_method_search"));
		
		$db_globle=new Application_Model_DbTable_DbGlobal();
		$row_status = $db_globle->getTbViews(17);
		$opt_s = array('-1'=>$this->tr->translate("BOOKING_STATUS"));
		$working_status = new Zend_Dojo_Form_Element_FilteringSelect("working_status");
		$working_status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		foreach ($row_status as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$working_status->setMultiOptions($opt_s);
		$working_status->setValue($request->getParam("working_status"));
		
		$this->addElements(array($from_book_date,$to_book_date,$search_tex,$customer,
				$driver_search,$vehicle_search,$agency_search,$payment_method_search,
				$working_status
				));
		return $this;
	}
	
	
}

