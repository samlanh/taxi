<?php
class Booking_Form_FrmBookingNew extends Zend_Dojo_Form{
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
	public function FromBooking($data=null){
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
		$return_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",'onchange'=>'calculateGrandtotal();'));
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
		$pickup_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside","onChange"=>"calculateGrandtotal();"));
		$pickup_location->setMultiOptions($opt_location);
		//$pickup_location->setValue(25);
		if($request->getParam("pickup_location")==""){
			$pickup_location->setValue(25);
		}else{
			$pickup_location->setValue($request->getParam("pickup_location"));
		}
		
		
		$return_location = new Zend_Form_Element_Select("return_location");
		$return_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside","onChange"=>"calculateGrandtotal();"));
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
		$pickup_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside","onChange"=>"calculateGrandtotal();"));
		$pickup_time->setMultiOptions($option_time);
		
		$return_time = new Zend_Dojo_Form_Element_FilteringSelect("return_time");
		$return_time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'calculateGrandtotal();'));
		$return_time->setMultiOptions($option_time);
		
		$option_minute = array('00'=>'00');
		$sum = 0;
		for($j=1;$j<=3;$j++){
			$min = $sum+15;
			$sum=$sum+15;
			$option_minute[$min] = $sum;
		}
		
		$pickup_minute = new Zend_Form_Element_Select("pickup_minute");
		
		$pickup_minute->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside","onChange"=>"calculateGrandtotal();"));
		$pickup_minute->setMultiOptions($option_minute);
		
		$return_minute = new Zend_Form_Element_Select("return_minute");
		
		$return_minute->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside","onChange"=>"calculateGrandtotal();"));
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
				'dojoType'=>$this->text,'class'=>"fullside",'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"
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
		$cash_pay->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside",'onKeyup'=>"checkMaxPay();"));
		
		$province = new Zend_Dojo_Form_Element_FilteringSelect("provice");
		$province->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCityPackage();'));
		$province->setMultiOptions($opt_location);
		
		$package = new Zend_Dojo_Form_Element_FilteringSelect("package");
		
		$other_fee1 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee1");
		$other_fee1->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee1->setValue(0);
		$other_fee_note1 = new Zend_Dojo_Form_Element_TextBox("other_fee_note1");
		$other_fee_note1->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee2 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee2");
		$other_fee2->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee2->setValue(0);
		$other_fee_note2 = new Zend_Dojo_Form_Element_TextBox("other_fee_note2");
		$other_fee_note2->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee3 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee3");
		$other_fee3->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee3->setValue(0);
		$other_fee_note3 = new Zend_Dojo_Form_Element_TextBox("other_fee_note3");
		$other_fee_note3->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee4 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee4");
		$other_fee4->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee4->setValue(0);
		$other_fee_note4 = new Zend_Dojo_Form_Element_TextBox("other_fee_note4");
		$other_fee_note4->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee5 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee5");
		$other_fee5->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee5->setValue(0);
		$other_fee_note5 = new Zend_Dojo_Form_Element_TextBox("other_fee_note5");
		$other_fee_note5->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee6 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee6");
		$other_fee6->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee6->setValue(0);
		
		$other_fee_note6 = new Zend_Dojo_Form_Element_TextBox("other_fee_note6");
		$other_fee_note6->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		$other_fee7 = new Zend_Dojo_Form_Element_NumberTextBox("other_fee7");
		$other_fee7->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		$other_fee7->setValue(0);
		
		$other_fee_note7 = new Zend_Dojo_Form_Element_TextBox("other_fee_note7");
		$other_fee_note7->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside","disabled"=>true,"onKeyup"=>"calculateGrandtotal();"));
		
		
		$opt_trip = array(1=>"One Way",2=>"Round Trip");
		$trip_type = new Zend_Dojo_Form_Element_FilteringSelect("trip_type");
		$trip_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside"));
		$trip_type->setMultiOptions($opt_trip);
		
		$rent_fee = new Zend_Dojo_Form_Element_NumberTextBox("rent_fee");
		$rent_fee->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"totalvehiclerentfee();"));
		$rent_fee->setValue(0);
		
		$long_dast = new Zend_Dojo_Form_Element_NumberTextBox("long_dast");
		$long_dast->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$long_dast->setValue(0);
		
		$discount_value = new Zend_Dojo_Form_Element_NumberTextBox("discount_value");
		$discount_value->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$discount_value->setValue(0);
		
		$refundable_deposit = new Zend_Dojo_Form_Element_NumberTextBox("refundable_deposit");
		$refundable_deposit->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"calculateGrandtotal(1), totalvehiclerentfee();"));
		$refundable_deposit->setValue(0);
		
		$total_rent_fee = new Zend_Dojo_Form_Element_NumberTextBox("total_rent_fee");
		$total_rent_fee->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","disabled"=>true,"onKeyup"=>"totalvehiclerentfee();"));
		$total_rent_fee->setValue(0);
		
		$sunday_price = new Zend_Dojo_Form_Element_NumberTextBox("sunday_price");
		$sunday_price->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$sunday_price->setValue(0);
		
		$sunday_price_remake = new Zend_Dojo_Form_Element_TextBox("sunday_price_remake");
		$sunday_price_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		$airport_price = new Zend_Dojo_Form_Element_NumberTextBox("airport_price");
		$airport_price->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$airport_price->setValue(0);
		
		$airport_price_remake = new Zend_Dojo_Form_Element_TextBox("airport_price_remake");
		$airport_price_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		$dropairport_price = new Zend_Dojo_Form_Element_NumberTextBox("dropairport_price");
		$dropairport_price->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$dropairport_price->setValue(0);
		
		$dropairport_price_remake = new Zend_Dojo_Form_Element_TextBox("dropairport_price_remake");
		$dropairport_price_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		$item_1 = new Zend_Dojo_Form_Element_NumberTextBox("item_1");
		$item_1->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$item_1->setValue(0);
		
		$item_1_remake = new Zend_Dojo_Form_Element_TextBox("item_1_remake");
		$item_1_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		$item_2 = new Zend_Dojo_Form_Element_NumberTextBox("item_2");
		$item_2->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$item_2->setValue(0);
		
		$item_2_remake = new Zend_Dojo_Form_Element_TextBox("item_2_remake");
		$item_2_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		$item_3 = new Zend_Dojo_Form_Element_NumberTextBox("item_3");
		$item_3->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside","onKeyup"=>"totalvehiclerentfee();"));
		$item_3->setValue(0);
		
		$item_3_remake = new Zend_Dojo_Form_Element_TextBox("item_3_remake");
		$item_3_remake->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",));
		
		if (!empty($data)){
			
			$pickup_date->setValue($data['pickup_date']);
			$return_date->setValue($data['return_date']);
			$pickup_location->setValue($data['pickup_location']);
			$return_location->setValue($data['dropoff_location']);
			
			$re_time = explode(":", $data['return_time']);
			$pick_time = explode(":", $data['pickup_time']);
			$return_time->setValue($re_time[0]);
			$pickup_time->setValue($re_time[1]);
			$pickup_minute->setValue($pick_time[0]);
			$return_minute->setValue($pick_time[1]);
			$_booking_no->setValue($data['booking_no']);
			$customer->setValue($data['customer_id']);
			
			$fly_no->setValue($data['fly_no']);
			$fly_date->setValue($data['fly_date']);
			$fly_destination->setValue($data['fly_destination']);
			$fly_time->setValue($data['fly_time_of_arrival']);
			
			
			
			if($data["payment_type"]==1){
				$card_name->setValue($data['visa_name']);
				$card_id->setValue($data['card_id']);
				$secu_code->setValue($data['secu_code']);
				$card_exp_date->setValue($data['card_exp_date']);
			}elseif($data["payment_type"]==2){
				$wu_code->setValue($data['card_id']);
			}elseif($data["payment_type"]==4){
				$cash_pay->setValue($data['total_paymented']);
			}
		}
		
		$this->addElements(array(
				$cash_pay,
				$other_fee_note1,
				$other_fee_note2,
				$other_fee_note3,
				$other_fee_note4,
				$other_fee_note5,
				$other_fee_note6,
				$other_fee_note7,
				$other_fee1,
				$other_fee2,
				$other_fee3,
				$other_fee4,
				$other_fee5,
				$other_fee6,
				$other_fee7,$province,
				$trip_type,
				$wu_code,
				$card_id,
				$card_exp_date,
				$card_name,
				$secu_code,$fly_no,
				$fly_date,$fly_destination,
				$fly_time,$customer,
				$cu_email,$cu_first_name,
				$cu_last_name,$cu_pass,
				$cu_phone,$cu_user_name,
				$gender,
				$pickup_minute,
				$return_minute,
				$return_time,
				$pickup_time,
				$pickup_date,
				$return_date,
				$pickup_location,
				$return_location,$_booking_no,
				
				$rent_fee,
				$long_dast,
				$discount_value,
				$refundable_deposit,
				$total_rent_fee,
				
				$sunday_price,
				$sunday_price_remake,
				$airport_price,
				$airport_price_remake,
				$dropairport_price,
				$dropairport_price_remake,
				$item_1,
				$item_1_remake,
				$item_2,
				$item_2_remake,
				$item_3,
				$item_3_remake
			));
		return $this;
	}
	public function FromBookingAgreement($data=null){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$_db = new Application_Model_DbTable_DbGlobal();
		
		$_owner_name = new Zend_Dojo_Form_Element_FilteringSelect("owner_name");
		$_owner_name->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
				'onchange'=>'getOwner();',
				));
		$option = array();
		$result = $_db->getAllNameOwner();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['owner_name'];
		}
		$_owner_name->setMultiOptions($option);
		
		$position = new Zend_Dojo_Form_Element_ValidationTextBox("position");
		$position->setAttribs(array('dojoType'=>$this->validate,'class'=>"fullside",'required'=>true));
		
		$id_card = new Zend_Dojo_Form_Element_NumberTextBox("id_card");
		$id_card->setAttribs(array('dojoType'=>$this->number,'class'=>"fullside",'required'=>true));
		
		$hand_phone = new Zend_Dojo_Form_Element_ValidationTextBox("hand_phone");
		$hand_phone->setAttribs(array('dojoType'=>$this->validate,'class'=>"fullside",'required'=>true));
		
		$email = new Zend_Dojo_Form_Element_ValidationTextBox("email");
		$email->setAttribs(array('dojoType'=>$this->validate,'class'=>"fullside",'required'=>true));
		
		$hotline = new Zend_Dojo_Form_Element_ValidationTextBox("hotline");
		$hotline->setAttribs(array('dojoType'=>$this->validate,'class'=>"fullside",'required'=>true));
		
		$witness = new Zend_Dojo_Form_Element_ValidationTextBox("witness");
		$witness->setAttribs(array('dojoType'=>$this->validate,'class'=>"fullside",'required'=>true));
		
		$agreement_date = new Zend_Dojo_Form_Element_DateTextBox("agreement_date");
		$agreement_date->setAttribs(array(
				'dojoType'=>$this->date,'class'=>"fullside",'required'=>true,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"
		));
		$agreement_date->setValue(date("Y-m-d"));
		
		$article = new Zend_Dojo_Form_Element_TextBox("article");
		$article->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$article->setValue("11 th");
		
		$toart1_id = new Zend_Dojo_Form_Element_TextBox("toart1_id");
		$toart1_id->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$toart1_id->setValue("33 rd");
		
		$art2_id = new Zend_Dojo_Form_Element_TextBox("art2_id");
		$art2_id->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$art2_id->setValue("13 th");
		
		$toart2_id = new Zend_Dojo_Form_Element_TextBox("toart2_id");
		$toart2_id->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$toart2_id->setValue("15 th");
		
		$art3_id = new Zend_Dojo_Form_Element_TextBox("art3_id");
		$art3_id->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$art3_id->setValue("16 th");
		
		$toart3_id = new Zend_Dojo_Form_Element_TextBox("toart3_id");
		$toart3_id->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'required'=>true));
		$toart3_id->setValue("17 th");
		
		
		$_regular_maintanance = new Zend_Dojo_Form_Element_FilteringSelect("regular_maintanance");
		$_regular_maintanance->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_regular_maintanance->setMultiOptions($option);
		
		$_unlimited_mileage = new Zend_Dojo_Form_Element_FilteringSelect("unlimited_mileage");
		$_unlimited_mileage->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_unlimited_mileage->setMultiOptions($option);
		
		$_repair_spare_part = new Zend_Dojo_Form_Element_FilteringSelect("repair_spare_part");
		$_repair_spare_part->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_repair_spare_part->setMultiOptions($option);
		
		$_insurance_coverage = new Zend_Dojo_Form_Element_FilteringSelect("insurance_coverage");
		$_insurance_coverage->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_insurance_coverage->setMultiOptions($option);
		
		$_fuel = new Zend_Dojo_Form_Element_FilteringSelect("fuel");
		$_fuel->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_fuel->setMultiOptions($option);
		
		$_fuel_full_tank = new Zend_Dojo_Form_Element_FilteringSelect("fuel_full_tank");
		$_fuel_full_tank->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
		));
		$option = array('1'=>'Yes','0'=>'No');
		$_fuel_full_tank->setMultiOptions($option);
		
		if (!empty($data)){
			
			$witness->setValue($data['witness']);
			$_owner_name->setValue($data['ownder_id']);
// 			$position->setValue($data['ownder_id']);
// 			$id_card->setValue($data['ownder_id']);
// 			$hand_phone->setValue($data['ownder_id']);
// 			$email->setValue($data['ownder_id']);
// 			$hotline->setValue($data['ownder_id']);
			
			$agreement_date->setValue(date("Y-m-d",strtotime($data['agreement_date'])));
			$article->setValue($data['art1_id']);
			$toart1_id->setValue($data['toart1_id']);
			$art2_id->setValue($data['art2_id']);
			$toart2_id->setValue($data['toart2_id']);
			$art3_id->setValue($data['art3_id']);
			$toart3_id->setValue($data['toart3_id']);
			
			$_regular_maintanance->setValue($data['regular_id']);
			$_unlimited_mileage->setValue($data['unlimited']);
			$_repair_spare_part->setValue($data['repare']);
			$_insurance_coverage->setValue($data['insurance']);
			$_fuel->setValue($data['fule']);
			$_fuel_full_tank->setValue($data['fuel_full']);
		}
		
		$this->addElements(array(
				$witness,
				$_owner_name,$position,$id_card,$hand_phone,$email,$hotline,
				$agreement_date,
				$article,$toart1_id,
				$art2_id,$toart2_id,
				$art3_id,$toart3_id,
				
				$_regular_maintanance,$_unlimited_mileage,
				$_repair_spare_part,$_insurance_coverage,
				$_fuel,$_fuel_full_tank
				
		));
		return $this;
	}
	
}

