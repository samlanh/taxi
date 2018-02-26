<?php
class Bookings_Form_FrmCarBooking extends Zend_Dojo_Form{
	protected $tr = null;
	protected $btn =null;//text validate
	protected $filter = null;
	protected $text =null;
	protected $validate = null;
	protected $date;
	protected $textarea=null;
	protected $number;
	protected $textareas=null;
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
		$this->textareas = 'dijit.form.Textarea';
	}
	public function FormBooking($data=null){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$booking_code = $_db->getNewCarBookingNO();
		$_booking_no = new Zend_Dojo_Form_Element_ValidationTextBox('booking_no');
		$_booking_no->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
				'style'=>'color:red',
				'placeholder'=>$this->tr->translate("Booking No"),
				'readOnly'=>true
		));
		$_booking_no->setValue($booking_code);
		
		$c_date = date("Y-m-d");
		$booking_date= new Zend_Dojo_Form_Element_DateTextBox("booking_date");
		$booking_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"
				));
		$booking_date->setValue($c_date);
		
		$delivery_date = new Zend_Form_Element_Text("delivery_date");
		$delivery_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"));
		if($request->getParam("return_date")==""){
			$delivery_date->setValue($c_date);
		}else{
			$delivery_date->setValue($request->getParam("delivery_date"));
		}
		
		$delivery_time = new Zend_Form_Element_Text("delivery_time");
		$delivery_time->setAttribs(array('dojoType'=>'dijit.form.TimeTextBox','class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',));
		$delivery_time->setValue('T00:00:00');
		
		$rows = $_db->getAllLocation();
		$opt_location = array(0=>$this->tr->translate(""),-1=>$this->tr->translate("ADD_NEW"));
		if(!empty($rows)){
			foreach($rows AS $row) {$opt_location[$row['id']]=$row['name'];};
		}
		$from_location = new Zend_Form_Element_Select("from_location");
		$from_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onchange'=>'getLocationPopUp(1)',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*'
		));
		$from_location->setMultiOptions($opt_location);
// 		if($request->getParam("from_location")==""){
// 			$from_location->setValue(25);
// 		}else{
// 			$from_location->setValue($request->getParam("pickup_location"));
// 		}
		$rows = $_db->getAllLocation();
		$opt_location = array(0=>$this->tr->translate(""),-1=>$this->tr->translate("ADD_NEW"));
		if(!empty($rows)){
			foreach($rows AS $row) {
				$opt_location[$row['id']]=$row['name'];
			};
		}
		$to_location = new Zend_Form_Element_Select("to_location");
		$to_location->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*','onchange'=>'getLocationPopUp(2)'));
		$to_location->setMultiOptions($opt_location);
		
		$row_cu = $_db->getAllCustomers();
		$opt_cu = array(0=>$this->tr->translate("SELECT_CUSTOMER"),-1=>$this->tr->translate("ADD_NEW"));
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*','onChange'=>'getCustomer();getCustomerPopUp()'));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["name"];
		}
		$customer->setMultiOptions($opt_cu);
		
		$row_dri = $_db->getAllDriver();
		$opt_dri = array(0=>$this->tr->translate("SELECT_DRIVER"),);
		$driver = new Zend_Dojo_Form_Element_FilteringSelect("driver");
		$driver->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getDriverInfoByid();'));
		foreach ($row_dri as $rs){
			$opt_dri[$rs["id"]] = $rs["name"];
		}
		$driver->setMultiOptions($opt_dri);
		
		$dbbooking = new Bookings_Model_DbTable_DbBooking();
		$row_veh = $dbbooking->getVehicleByCategory();
		$opt_vehi = array(0=>$this->tr->translate("SELECT_VEHICLE"));
		$vehicle = new Zend_Dojo_Form_Element_FilteringSelect("vehicle");
		$vehicle->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCarInfor();'));
		foreach ($row_veh as $rs){
			$opt_vehi[$rs["id"]] = $rs["name"];
		}
		$vehicle->setMultiOptions($opt_vehi);
		
		$row_agen = $_db->getAllAgency();
		$opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"),'-1'=>$this->tr->translate("ADD_NEW"));
		$agency = new Zend_Dojo_Form_Element_FilteringSelect("agency");
		$agency->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*','onChange'=>'getAgent();getAgencyPopUp()'));
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency->setMultiOptions($opt_agen);
		
		
		
		$price = new Zend_Dojo_Form_Element_NumberTextBox("price");
		$price->setAttribs(
				array('dojoType'=>$this->number,
					'class'=>"fullside",
					'onKeyup'=>'getPice();',
				));
		$price->setValue(0);
		
		$commision_fee = new Zend_Dojo_Form_Element_NumberTextBox("commision_fee");
		$commision_fee->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
					//	'onChange'=>'CalculateTotal();',
				));
		$commision_fee->setValue(0);
		
		$other_fee = new Zend_Dojo_Form_Element_NumberTextBox("other_fee");
		$other_fee->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'onKeyup'=>'CalculateTotal();',
				));
		$other_fee->setValue(0);
		
		$total = new Zend_Dojo_Form_Element_NumberTextBox("total");
		$total->setAttribs(
				array('dojoType'=>$this->number,
						'onKeyup'=>'CalculateTotal();',
						'class'=>"fullside",
						'required'=>true,
				));
		$total->setValue(0);
		
		$remark = new Zend_Dojo_Form_Element_TextBox("remark");
		$remark->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
		
		$note = new Zend_Dojo_Form_Element_TextBox("note");
		$note->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
		
		$other_booking_no = new Zend_Dojo_Form_Element_TextBox("other_booking_no");
		$other_booking_no->setAttribs(array('dojoType'=>$this->text,'class'=>"fullside",'onkeyup'=>'checkBookNo();'));
		
		$row_payment = $_db->getVewOptoinTypeByTypes(11);
		$opt_payment = array(0=>$this->tr->translate("SELECT_PAYMENT_METHOD"));
		$payment_method = new Zend_Dojo_Form_Element_FilteringSelect("payment_method");
		$payment_method->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_payment as $rs){
			$opt_payment[$rs["id"]] = $rs["name"];
		}
		$payment_method->setMultiOptions($opt_payment);
		
		$total_payment = new Zend_Dojo_Form_Element_NumberTextBox("total_payment");
		$total_payment->setAttribs(
				array('dojoType'=>$this->number,
						'readonly'=>'readonly',
						'class'=>"fullside",
				));
		$total_payment->setValue(0);
		
		$total_paid = new Zend_Dojo_Form_Element_NumberTextBox("total_paid");
		$total_paid->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'onKeyup'=>'calculatebalance();',
				));
		$total_paid->setValue(0);
		
		$balance = new Zend_Dojo_Form_Element_NumberTextBox("balance");
		$balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$balance->setValue(0);
		$payment_note = new Zend_Dojo_Form_Element_TextBox("payment_note");
		$payment_note->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
		
		$_fly_no = new Zend_Dojo_Form_Element_TextBox('fly_no');
		$_fly_no->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'style'=>'color:red',
				'placeholder'=>$this->tr->translate("FLY_NO")
		));
		
		$cus_name = new Zend_Dojo_Form_Element_TextBox('cus_name');
		$cus_name->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("CUSTOMER_NAME")
		));
		
		$cus_phone = new Zend_Dojo_Form_Element_TextBox('cus_phone');
		$cus_phone->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("CUS_PHONE"),
				'required'=>true
		));
		
		$cus_email = new Zend_Dojo_Form_Element_TextBox('cus_email');
		$cus_email->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("CUS_EMAIL"),
				'required'=>true
		));
		
		$driver_fee = new Zend_Dojo_Form_Element_NumberTextBox("driver_fee");
		$driver_fee->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'required'=>true
				));
		$driver_fee->setValue(0);
		
		$work_s = $_db->getTbViews(17);
		$opt_s=array();
		$working_status = new Zend_Dojo_Form_Element_FilteringSelect("working_status");
		$working_status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($work_s as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$working_status->setMultiOptions($opt_s);
		
		$work_s = $_db->getTbViews(18);
		$opt_s=array();
		$paid_status = new Zend_Dojo_Form_Element_FilteringSelect("paid_status");
		$paid_status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($work_s as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$paid_status->setMultiOptions($opt_s);
		
		$work_s = $_db->getTbViews(19);
		$opt_s=array();
		$balanc_status = new Zend_Dojo_Form_Element_FilteringSelect("balanc_status");
		$balanc_status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($work_s as $rs){
			$opt_s[$rs["key_code"]] = $rs["name_en"];
		}
		$balanc_status->setMultiOptions($opt_s);
		 
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_veh_typ=$db->getAllVehicleType();
		$opt_payment = array(0=>$this->tr->translate("SELECT_VECHICLE_TYPE"),-1=>$this->tr->translate("Add Vehicle Type"));
		$vehicle_type = new Zend_Dojo_Form_Element_FilteringSelect("vehicle_type");
		$vehicle_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',
				      'onchange'=>'getPopupFormVehicleType()'
				));
		foreach ($rows_veh_typ as $rs){
			$opt_payment[$rs["id"]] = $rs["title"];
		}
		$vehicle_type->setMultiOptions($opt_payment);
		
		if (!empty($data)){
			$dbbooking = new Bookings_Model_DbTable_DbBooking();
			$chekcpayment = $dbbooking->checkBookingHasPayment($data['id']);
			
			$_booking_no->setValue($data['booking_no']);
			$other_booking_no->setValue($data['status_working']);
			$customer->setValue($data['customer_id']);
			$driver->setValue($data['driver_id']);
			$vehicle->setValue($data['vehicle_id']);
			$agency->setValue($data['agency_id']);
			$from_location->setValue($data['from_location']);
			$to_location->setValue($data['to_location']);
			$booking_date->setValue(date("Y-m-d",strtotime($data['booking_date'])));
			$delivery_date->setValue(date("Y-m-d",strtotime($data['delivey_date'])));
			$commision_fee->setValue($data['commision_fee']);
			$price->setValue($data['price']);
			$other_fee->setValue($data['other_fee']);
			$total->setValue($data['total']);
			$remark->setValue($data['remark']);
			$other_booking_no->setValue($data['payment_booking_no']);
			$total_paid->setValue($data['paid']);
			$balance->setValue($data['balance']);
			$working_status->setValue($data['status_working']);
			$vehicle_type->setValue($data['vehicletype_id']);
			$note->setValue($data['note']);
			$cus_name->setValue($data['last_name']);
			$cus_phone->setValue($data['phone']);
			$cus_email->setValue($data['email']);
			$paid_status->setValue($data['paid_status']);
			$balanc_status->setValue($data['balance_status']);
			
			$_fly_no->setValue($data['fly_no']);
			$delivery_time->setValue($data['delivey_time']);
			$driver_fee->setValue($data['driver_fee']);
			if ($data['is_customer_paid']==1){
				$customer->setAttribs(array('readonly'=>'readonly',));
				$agency->setAttribs(array('readonly'=>'readonly',));
				$commision_fee->setAttribs(array('readonly'=>'readonly',));
				$other_fee->setAttribs(array('readonly'=>'readonly',));
				$price->setAttribs(array('readonly'=>'readonly',));
				$total->setAttribs(array('readonly'=>'readonly',));
				if ($data['is_paid_to_driver']==1){
				$driver_fee->setAttribs(array('readonly'=>'readonly',));
				}
			}
			if ($data['is_paid_to_driver']==1){
				$driver_fee->setAttribs(array('readonly'=>'readonly',));
			}
			if ($data['is_paid_commission']==1){
				$commision_fee->setAttribs(array('readonly'=>'readonly',));
				$agency->setAttribs(array('readonly'=>'readonly',));
			}
			
			$total_paid->setValue(0);
			$total_payment->setValue($data['grand_total']);
			if (!empty($chekcpayment)){
				$balance->setValue(0);
				$total_paid->setValue(0);
				$total_payment->setValue(0);
				$payment_method->setAttribs(array('readonly'=>'readonly',));
				$total_payment->setAttribs(array('readonly'=>'readonly',));
				$balance->setAttribs(array('readonly'=>'readonly',));
				$total_paid->setAttribs(array('readonly'=>'readonly',));
				$payment_note->setAttribs(array('readonly'=>'readonly',));
			}
		}
		
		$this->addElements(array(
				$_booking_no,
				$customer,
				$_fly_no,
				$driver,
				$vehicle,
				$agency,
				$from_location,
				$to_location,
				$booking_date,
				$delivery_time,
				$delivery_date,
				$price,
				$commision_fee,
				$other_fee,
				$total,
				$remark,
				$driver_fee,
				$vehicle_type,
				$payment_method,
				$total_payment,
				$balance,
				$total_paid,
				$payment_note,
				$other_booking_no,
				$working_status,
				$note,
				$cus_name,
				$cus_phone,
				$cus_email,
				$paid_status,
				$balanc_status
			));
		return $this;
	}
	
}

