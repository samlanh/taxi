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
		$from_book_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		if($request->getParam("from_book_date")==""){
			//$from_book_date->setValue($c_date);
		}else{
			$from_book_date->setValue($request->getParam("from_book_date"));
		}
		
		$to_book_date = new Zend_Dojo_Form_Element_DateTextBox("to_book_date");
		$to_book_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		if($request->getParam("to_book_date")==""){
			//$to_book_date->setValue($c_date);
		}else{
			$to_book_date->setValue($request->getParam("to_book_date"));
		}
		
		$search_tex = new Zend_Dojo_Form_Element_TextBox("search_text");
		$search_tex->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		$search_tex->setValue($request->getParam("search_text"));
		
		$row_cu = $_db->getAllCustomers();
		$opt_cu = array(0=>$this->tr->translate("SELECT_CUSTOMER"));
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCustomer();',
				'autoComplete'=>'false', 'queryExpr'=>'*${0}*',
				));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["name"];
		}
		$customer->setMultiOptions($opt_cu);
		$customer->setValue($request->getParam("customer"));
		
		$row_dri = $_db->getAllDriver();
		$opt_dri = array(0=>$this->tr->translate("SELECT_DRIVER"));
		$driver_search = new Zend_Dojo_Form_Element_FilteringSelect("driver_search");
		$driver_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		foreach ($row_dri as $rs){
			$opt_dri[$rs["id"]] = $rs["name"];
		}
		$driver_search->setMultiOptions($opt_dri);
		$driver_search->setValue($request->getParam("driver_search"));
		
		$row_veh = $_db->getVehicleHasDriver();
		$opt_vehi = array(0=>$this->tr->translate("SELECT_VEHICLE"));
		$vehicle_search = new Zend_Dojo_Form_Element_FilteringSelect("vehicle_search");
		$vehicle_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		foreach ($row_veh as $rs){
			$opt_vehi[$rs["id"]] = $rs["name"];
		}
		$vehicle_search->setMultiOptions($opt_vehi);
		$vehicle_search->setValue($request->getParam("vehicle_search"));
		
		$row_agen = $_db->getAllAgency();
		$opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"));
		$agency_search = new Zend_Dojo_Form_Element_FilteringSelect("agency_search");
		$agency_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',
				 'autoComplete'=>'false', 'queryExpr'=>'*${0}*',
				));
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency_search->setMultiOptions($opt_agen);
		$agency_search->setValue($request->getParam("agency_search"));
		
		$row_payment = $_db->getVewOptoinTypeByTypes(11);
		$opt_payment = array(0=>$this->tr->translate("SELECT_PAYMENT_METHOD"));
		$payment_method_search = new Zend_Dojo_Form_Element_FilteringSelect("payment_method_search");
		$payment_method_search->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		foreach ($row_payment as $rs){
			$opt_payment[$rs["id"]] = $rs["name"];
		}
		$payment_method_search->setMultiOptions($opt_payment);
		$payment_method_search->setValue($request->getParam("payment_method_search"));
		
		$db_globle=new Application_Model_DbTable_DbGlobal();
		$row_status = $db_globle->getTbViews(17);
		$opt_s = array('-1'=>$this->tr->translate("BOOKING_STATUS"));
		$working_status = new Zend_Dojo_Form_Element_FilteringSelect("working_status");
		$working_status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		foreach ($row_status as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$working_status->setValue($request->getParam("working_status"));
		$working_status->setMultiOptions($opt_s);
		
		
		$row_status = $db_globle->getTbViews(5);
		$opt_s = array('-1'=>$this->tr->translate("SELECT_STATUS"));
		$status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		foreach ($row_status as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$status->setMultiOptions($opt_s);
		$status->setValue($request->getParam("status"));
		
		
		$opt_s = array('1'=>$this->tr->translate("Is Paid"),'0'=>$this->tr->translate("Not Paid"));
		$is_paid = new Zend_Dojo_Form_Element_FilteringSelect("is_paid");
		$is_paid->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		$is_paid->setMultiOptions($opt_s);
		$is_paid->setValue($request->getParam("is_paid"));
		
		/*$delivery_time = new Zend_Form_Element_Text("delivery_time");
		$delivery_time->setAttribs(array('dojoType'=>'dijit.form.TimeTextBox','class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		$working_status->setValue($request->getParam("delivery_time"));
		*/
		
		$db_glob=new Application_Model_GlobalClass();
		$time=$db_glob->getTime();
		$delivery_time = new Zend_Form_Element_Select("delivery_time");
		$delivery_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*'
		));
		$delivery_time->setMultiOptions($time);
		$delivery_time->setValue($request->getParam("delivery_time"));
		
		$start_time = new Zend_Form_Element_Select("start_time");
		$start_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*'
		));
		$start_time->setMultiOptions($time);
		$start_time->setValue($request->getParam("start_time"));
		
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_veh_typ=$db->getAllVehicleType();
		$opt_payment = array(0=>$this->tr->translate("SELECT_VECHICLE_TYPE"),);
		$vehicle_type = new Zend_Dojo_Form_Element_FilteringSelect("vehicle_type");
		$vehicle_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*', ));
		foreach ($rows_veh_typ as $rs){
			$opt_payment[$rs["id"]] = $rs["title"];
		}
		$vehicle_type->setMultiOptions($opt_payment);
		$vehicle_type->setValue($request->getParam("vehicle_type"));
		
		$rows_plate=$db->getAllPlatenNo();
		$opt_payment = array(0=>$this->tr->translate("Plate Number"),);
		$plate_number = new Zend_Dojo_Form_Element_FilteringSelect("plate_number");
		$plate_number->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*', ));
		foreach ($rows_plate as $rs){
		    $opt_payment[$rs["id"]] = $rs["title"];
		}
		$plate_number->setMultiOptions($opt_payment);
		$plate_number->setValue($request->getParam("plate_number"));
		
		$rows_plate=$db->getAllCustomerCarrental();
		$opt_payment = array(0=>$this->tr->translate("Lessee Name"),);
		$lessee_name = new Zend_Dojo_Form_Element_FilteringSelect("lessee_name");
		$lessee_name->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*', ));
		foreach ($rows_plate as $rs){
		    $opt_payment[$rs["id"]] = $rs["name"];
		}
		$lessee_name->setMultiOptions($opt_payment);
		$lessee_name->setValue($request->getParam("lessee_name"));
		
		$opt_s = array('2'=>$this->tr->translate("DATE_TO_RECEIVE"),'1'=>$this->tr->translate("BOOKING_DATE"),);
		$date_type = new Zend_Dojo_Form_Element_FilteringSelect("date_type");
		$date_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		$date_type->setMultiOptions($opt_s);
		$date_type->setValue($request->getParam("date_type"));
		
		$start_date = new Zend_Dojo_Form_Element_DateTextBox("start_date");
		$start_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		if($request->getParam("start_date")==""){
			//$to_book_date->setValue($c_date);
		}else{
			$start_date->setValue($request->getParam("start_date"));
		}
		
		$end_date = new Zend_Dojo_Form_Element_DateTextBox("end_date");
		$end_date->setAttribs(array('dojoType'=>$this->date,'class'=>"fullside",'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		if($request->getParam("end_date")==""){
			$end_date->setValue($c_date);
		}else{
			$end_date->setValue($request->getParam("end_date"));
		}
		
		 
		$opt_s = array('-1'=>$this->tr->translate("Select All"),'1'=>$this->tr->translate("Stop"),'0'=>$this->tr->translate("Renting"),);
		$is_return = new Zend_Dojo_Form_Element_FilteringSelect("is_return");
		$is_return->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		$is_return->setMultiOptions($opt_s);
		$is_return->setValue($request->getParam("is_return"));
		
		$this->addElements(array($lessee_name,$plate_number,$is_return,$is_paid,$end_date,$start_date,$start_time,$status,$date_type,$vehicle_type,$delivery_time,$from_book_date,$to_book_date,$search_tex,$customer,
				 $driver_search,$vehicle_search,$agency_search,$payment_method_search,
				$working_status
				));
		return $this;
	}
}

