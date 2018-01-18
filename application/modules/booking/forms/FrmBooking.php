<?php
class Booking_Form_FrmBooking extends Zend_Dojo_Form{
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
	public function FromBooking(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$booking_code = $_db->getNewBookingCode();
		$_booking_no = new Zend_Dojo_Form_Element_ValidationTextBox('booking_no');
		$_booking_no->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
				'readonly'=>true,
				'style'=>'color:red',
				'placeholder'=>$this->tr->translate("Booking No")
		));
		$_booking_no->setValue($booking_code);
		
		$c_date = date("Y-m-d");
		$pickup_date = new Zend_Dojo_Form_Element_DateTextBox("pickup_date");
		$pickup_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"
				));
		$pickup_date->setValue($c_date);
		$return_date = new Zend_Form_Element_Text("return_date");
		$return_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",));
		if($request->getParam("return_date")==""){
			$return_date->setValue($c_date);
		}else{
			$return_date->setValue($request->getParam("return_date"));
		}
		
		$rows = $_db->getAllProvince();
		$opt_location = array(0=>$this->tr->translate("CHOOSE_LOCTION"));
		if(!empty($rows)){
			foreach($rows AS $row) {$opt_location[$row['id']]=$row['name'];};
		}
		$pickup_location = new Zend_Form_Element_Select("pickup_location");
		$pickup_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$pickup_location->setMultiOptions($opt_location);
		//$pickup_location->setValue(25);
		if($request->getParam("pickup_location")==""){
			$pickup_location->setValue(25);
		}else{
			$pickup_location->setValue($request->getParam("pickup_location"));
		}
		
		
		$return_location = new Zend_Form_Element_Select("return_location");
		$return_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$return_location->setMultiOptions($opt_location);
		if($request->getParam("return_location")==""){
			$return_location->setValue(25);
		}else{
			$return_location->setValue($request->getParam("return_location"));
		}
		for($i=1;$i<12;$i++){
			$time = $i+6;
			if($i+6<=12){
				$icon = " AM";
			}else{
				$icon = " PM";
			}
			$value = $time.$icon;
			$option_time[$time] = $value;
		}
		$pickup_time = new Zend_Dojo_Form_Element_FilteringSelect("pickup_time");
		$pickup_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$pickup_time->setMultiOptions($option_time);
		
		$return_time = new Zend_Dojo_Form_Element_FilteringSelect("return_time");
		$return_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'calculateDate();'));
		$return_time->setMultiOptions($option_time);
		
		$option_minute = array('00'=>'00');
		$sum = 0;
		for($j=1;$j<=3;$j++){
			$min = $sum+15;
			$sum=$sum+15;
			$option_minute[$min] = $sum;
		}
		
		$pickup_minute = new Zend_Form_Element_Select("pickup_minute");
		
		$pickup_minute->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$pickup_minute->setMultiOptions($option_minute);
		
		$return_minute = new Zend_Form_Element_Select("return_minute");
		
		$return_minute->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$return_minute->setMultiOptions($option_minute);
		
		$db_booking = new Booking_Model_DbTable_DbBooking();
		$row_cu = $db_booking->getIdNamecustomer();
		$opt_cu = array(''=>'Select Customer');
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCustomer();'));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["first_name"]." ".$rs["last_name"];
		}
		$customer->setMultiOptions($opt_cu);
		
		$cu_email = new Zend_Dojo_Form_Element_TextBox("cu_email");
		$cu_email->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
		$cu_phone = new Zend_Dojo_Form_Element_TextBox("cu_phone");
		$cu_phone->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
// 		$cu_phone = new Zend_Dojo_Form_Element_TextBox("cu_phone");
// 		$cu_phone->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside"));
		
		$cu_user_name = new Zend_Dojo_Form_Element_TextBox("cu_user_name");
		$cu_user_name->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
		$cu_pass = new Zend_Dojo_Form_Element_PasswordTextBox("cu_pass");
		$cu_pass->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
		$cu_first_name = new Zend_Dojo_Form_Element_TextBox("cu_first_name");
		$cu_first_name->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
		$cu_last_name = new Zend_Dojo_Form_Element_TextBox("cu_last_name");
		$cu_last_name->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		
		$gender = new Zend_Dojo_Form_Element_FilteringSelect("gender");
		$gender->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$opt_gender = array(1=>"Male",2=>"Female");
		$gender->setMultiOptions($opt_gender);
		
		
		$card_id = new Zend_Dojo_Form_Element_TextBox('card_id');
		$card_id->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside",));
		
		$wu_code = new Zend_Dojo_Form_Element_NumberTextBox("wu_code");
		$wu_code->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside",));
		
		$card_issue_date = new zend_form_element_text("card_issue_date");
		$card_issue_date->setAttribs(array(
				'dojoType'=>$this->text,'class'=>"fullside",'required'=>true
		));
		
		$card_exp_date = new Zend_Dojo_Form_Element_DateTextBox('card_exp_date');
		$card_exp_date->setAttribs(array(
				'dojoType'=>$this->date,'class'=>"fullside",
		));
		
		$secu_code = new Zend_Dojo_Form_Element_NumberTextBox('secu_code');
		$secu_code->setAttribs(array(
				'dojoType'=>$this->number,'class'=>"fullside",
		));
		
		$card_name = new Zend_Dojo_Form_Element_TextBox('card_name');
		$card_name->setAttribs(array(
				'dojoType'=>$this->text,'class'=>"fullside",
				));
		
		$fly_no = new Zend_Dojo_Form_Element_TextBox("fly_no");
		$fly_no->setAttribs(array(
				'dojoType'=>$this->text,'class'=>"fullside"
		));
		
		$fly_date = new Zend_Dojo_Form_Element_DateTextBox("fly_date");
		$fly_date->setAttribs(array(
				'dojoType'=>$this->date,'class'=>"fullside"
		));
		$fly_date->setValue($c_date);
		
		$fly_time = new Zend_Dojo_Form_Element_TextBox("fly_time");
		$fly_time->setAttribs(array(
				'dojoType'=>$this->text,'class'=>"fullside"
		));
		
		$fly_destination = new Zend_Dojo_Form_Element_Textarea("fly_destination");
		$fly_destination->setAttribs(array(
				'dojoType'=>$this->textarea,'class'=>"fullside"
		));
		$dob = new Zend_Form_Element_Text("dob");
		$dob->setAttribs(array(
				'style'=>'width: 100% !important;padding:1px !important;height: 30px;',
				'class'=>'control_style','placeholder'=>'Date of Birth : d-m-YYYY'
		));
		$cash_pay = new Zend_Dojo_Form_Element_NumberTextBox("cash_pay");
		$cash_pay->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside"));
		
		$province = new Zend_Dojo_Form_Element_FilteringSelect("provice");
		$province->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCityPackage();'));
		$province->setMultiOptions($opt_location);
		
		$package = new Zend_Dojo_Form_Element_FilteringSelect("package");
		
		$other_fee1 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee1");
		$other_fee1->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note1 = new Zend_Dojo_Form_Element_TextBox("other_fee_note1");
		$other_fee_note1->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee2 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee2");
		$other_fee2->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note2 = new Zend_Dojo_Form_Element_TextBox("other_fee_note2");
		$other_fee_note2->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee3 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee3");
		$other_fee3->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note3 = new Zend_Dojo_Form_Element_TextBox("other_fee_note3");
		$other_fee_note3->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee4 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee4");
		$other_fee4->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note4 = new Zend_Dojo_Form_Element_TextBox("other_fee_note4");
		$other_fee_note4->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee5 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee5");
		$other_fee5->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note5 = new Zend_Dojo_Form_Element_TextBox("other_fee_note5");
		$other_fee_note5->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee6 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee6");
		$other_fee6->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note6 = new Zend_Dojo_Form_Element_TextBox("other_fee_note6");
		$other_fee_note6->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		$other_fee7 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee7");
		$other_fee7->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true));
		
		$other_fee_note7 = new Zend_Dojo_Form_Element_TextBox("other_fee_note7");
		$other_fee_note7->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true));
		
		
		$opt_trip = array(1=>"One Way",2=>"Round Trip");
		$trip_type = new Zend_Dojo_Form_Element_FilteringSelect("trip_type");
		$trip_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$trip_type->setMultiOptions($opt_trip);
		$this->addElements(array($cash_pay,$other_fee_note1,$other_fee_note2,$other_fee_note3,$other_fee_note4,$other_fee_note5,$other_fee_note6,$other_fee_note7,$other_fee1,$other_fee2,$other_fee3,$other_fee4,$other_fee5,$other_fee6,$other_fee7,$province,$trip_type,$wu_code,$card_id,$card_exp_date,$card_name,$secu_code,$fly_no,$fly_date,$fly_destination,$fly_time,$customer,$cu_email,$cu_first_name,$cu_last_name,$cu_pass,$cu_phone,$cu_user_name,$gender,$pickup_minute,$return_minute,$return_time,$pickup_time,$pickup_date,$return_date,$pickup_location,$return_location,$_booking_no));
		return $this;
	}
	
	
}

