<?php
class Bookings_Form_FrmCarrental extends Zend_Dojo_Form{
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
	
	public function FormCarrental($data=null){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$_db = new Application_Model_DbTable_DbGlobal();
		$booking_code = $_db->getNewCarrentalNO();
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
		$rent_date= new Zend_Dojo_Form_Element_DateTextBox("rent_date");
		$rent_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",'readonly'=>'readonly'
				));
		$rent_date->setValue($c_date);
		
		$start_date = new Zend_Form_Element_Text("start_date");
		$start_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"));
		if($request->getParam("start_date")==""){
			$start_date->setValue($c_date);
		}else{
			$start_date->setValue($request->getParam("start_date"));
		}
		$return_date = new Zend_Form_Element_Text("return_date");
		$return_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside"));
		if($request->getParam("return_date")==""){
			$return_date->setValue($c_date);
		}else{
			$return_date->setValue($request->getParam("return_date"));
		}
		$return_time = new Zend_Form_Element_Text("return_time");
		$return_time->setAttribs(array('dojoType'=>'dijit.form.TimeTextBox','class'=>"fullside"));
		$return_time->setValue('T00:00:00');
		
		
		$row_cu = $_db->getAllCustomers();
		$opt_cu = array(0=>$this->tr->translate("SELECT_CUSTOMER"));
		$customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
		$customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getCustomer();'));
		foreach ($row_cu as $rs){
			$opt_cu[$rs["id"]] = $rs["name"];
		}
		$customer->setMultiOptions($opt_cu);
		
		
		$row_agen = $_db->getAllAgency();
		$opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"));
		$agency = new Zend_Dojo_Form_Element_FilteringSelect("agency");
		$agency->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAgent();'));
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency->setMultiOptions($opt_agen);
		
		$row_veh = $_db->getVehicleHasDriver();
		$opt_vehi = array(0=>$this->tr->translate("SELECT_VEHICLE"));
		$vehicle = new Zend_Dojo_Form_Element_FilteringSelect("vehicle");
		$vehicle->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getVehicleInfo();'));
		foreach ($row_veh as $rs){
			$opt_vehi[$rs["id"]] = $rs["name"];
		}
		$vehicle->setMultiOptions($opt_vehi);
		
		$remark = new Zend_Dojo_Form_Element_TextBox("remark");
		$remark->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
		
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
		
		$total_rent_fee = new Zend_Dojo_Form_Element_NumberTextBox("total_rent_fee");
		$total_rent_fee->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_rent_fee->setValue(0);
		
		$refundable_deposit = new Zend_Dojo_Form_Element_NumberTextBox("refundable_deposit");
		$refundable_deposit->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'onKeyup'=>'calculateTotal();',
				));
		$refundable_deposit->setValue(0);
		
		$phone = new Zend_Dojo_Form_Element_ValidationTextBox('phone');
		$phone->setAttribs(array(
		    'dojoType'=>'dijit.form.ValidationTextBox',
		    'placeholder'=>$this->tr->translate("Phone Number")
		));
		 
		
		if (!empty($data)){
			$dbbooking = new Bookings_Model_DbTable_DbBooking();
			$chekcpayment = $dbbooking->checkBookingHasPayment($data['id']);
			
			$_booking_no->setValue($data['booking_no']);
			$customer->setValue($data['customer_id']);
			$agency->setValue($data['agency_id']);
			$rent_date->setValue(date("Y-m-d",strtotime($data['booking_date'])));
			$return_date->setValue(date("Y-m-d",strtotime($data['delivey_date'])));
			$return_time->setValue($data['delivey_time']);
			
		}
		
		$this->addElements(array(
				$_booking_no,
				$customer,
				$agency,
				$rent_date,
				$start_date,
				$return_time,
				$return_date,
				$remark,
				$vehicle,
				
				$refundable_deposit,
				$total_rent_fee,
				$total_payment,
				$total_paid,
				$balance,
		        $phone
			));
		return $this;
	}
	
	public function FormRenntCarental($data=null){
	    $request=Zend_Controller_Front::getInstance()->getRequest();
	    $_db = new Application_Model_DbTable_DbGlobal();
	    $booking_code = $_db->getNewCarrentalNO();
	    $rent_no = new Zend_Dojo_Form_Element_ValidationTextBox('rent_no');
	    $rent_no->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
// 	        'required'=>true,
// 	        'readonly'=>true,
// 	        'style'=>'color:red',
	        'placeholder'=>$this->tr->translate("Rent No")
	    ));
	    $rent_no->setValue($booking_code);
	    
	    $fix_name = new Zend_Dojo_Form_Element_ValidationTextBox('fix_name');
	    $fix_name->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
	        // 	        'required'=>true,
	    // 	        'readonly'=>true,
	    // 	        'style'=>'color:red',
	        'placeholder'=>$this->tr->translate("Rent No")
	    ));
	    
	    $phone = new Zend_Dojo_Form_Element_ValidationTextBox('phone');
	    $phone->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
	     // 	        'required'=>true,
	    // 	        'readonly'=>true,
	    // 	        'style'=>'color:red',
	        'placeholder'=>$this->tr->translate("Phone No")
	    ));
	    
	    $address = new Zend_Dojo_Form_Element_ValidationTextBox('address');
	    $address->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
	    ));
	    
	    $color = new Zend_Dojo_Form_Element_ValidationTextBox('color');
	    $color->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
	    ));
	    
	    $c_date = date("Y-m-d");
	    $rent_date= new Zend_Dojo_Form_Element_DateTextBox("rent_date");
	    $rent_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
	    ));
	    $rent_date->setValue($c_date);
	    
	    $return_date= new Zend_Dojo_Form_Element_DateTextBox("return_date");
	    $return_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
	    ));
	    $return_date->setValue($c_date);
	    
	    $repair_date= new Zend_Dojo_Form_Element_DateTextBox("repair_date");
	    $repair_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
	    ));
	    $repair_date->setValue($c_date);
	    
	    $payment_date= new Zend_Dojo_Form_Element_DateTextBox("payment_date");
	    $payment_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
	    ));
	    $payment_date->setValue($c_date);
	    
	    $expired_date= new Zend_Dojo_Form_Element_DateTextBox("expired_date");
	    $expired_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
	    ));
	    $expired_date->setValue($c_date);
	    
	    $row_agen = $_db->getAllAgency();
	    $opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"));
	    $agency = new Zend_Dojo_Form_Element_FilteringSelect("agency");
	    $agency->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAgent();'));
	    foreach ($row_agen as $rs){
	        $opt_agen[$rs["id"]] = $rs["name"];
	    }
	    $agency->setMultiOptions($opt_agen);
	    
	    $row_agen = $_db->getAllAgency();
	    $time = array(0=>$this->tr->translate("SELECT_TIME"));
	    $time = new Zend_Dojo_Form_Element_FilteringSelect("time");
	    $time->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAgent();'));
// 	    foreach ($row_agen as $rs){
// 	        $opt_agen[$rs["id"]] = $rs["name"];
// 	    }
// 	    $time->setMultiOptions($opt_agen);
	    
	    $total_paid = new Zend_Dojo_Form_Element_NumberTextBox("total_paid");
	    $total_paid->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            'onKeyup'=>'calculatebalance();',
	        ));
	    $total_paid->setValue(0);
	    
	    if (!empty($data)){
	        $dbbooking = new Bookings_Model_DbTable_DbBooking();
	        $chekcpayment = $dbbooking->checkBookingHasPayment($data['id']);
	        
	    }
	    
	  //  $row_veh = $_db->getVehicleHasDriver();
	    $opt_vehi = array(0=>$this->tr->translate("SELECT_CUSTOMER"));
	    $customer = new Zend_Dojo_Form_Element_FilteringSelect("customer");
	    $customer->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getVehicleInfo();'));
// 	    foreach ($row_veh as $rs){
// 	        $opt_vehi[$rs["id"]] = $rs["name"];
// 	    }
// 	    $vehicle->setMultiOptions($opt_vehi);

	    $row_veh = $_db->getVehicleHasDriver();
	    $opt_vehi = array(0=>$this->tr->translate("SELECT_VEHICLE"));
	    $vehicle = new Zend_Dojo_Form_Element_FilteringSelect("vehicle");
	    $vehicle->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getVehicleInfo();'));
	    foreach ($row_veh as $rs){
	        $opt_vehi[$rs["id"]] = $rs["name"];
	    }
	    $vehicle->setMultiOptions($opt_vehi);
	    
	    $return_money = new Zend_Dojo_Form_Element_NumberTextBox("return_money");
	    $return_money->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	             
	        ));
	    $return_money->setValue(0);
	    
	    $paid = new Zend_Dojo_Form_Element_NumberTextBox("paid");
	    $paid->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            
	        ));
	    $paid->setValue(0);
	    
	    $cost_month = new Zend_Dojo_Form_Element_NumberTextBox("cost_month");
	    $cost_month->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            
	        ));
	    $cost_month->setValue(0);
	    
	    
	    
	    $remark = new Zend_Dojo_Form_Element_TextBox("remark");
	    $remark->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
	    
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
	    
	    $total_rent_fee = new Zend_Dojo_Form_Element_NumberTextBox("total_rent_fee");
	    $total_rent_fee->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            'readonly'=>'readonly',
	        ));
	    $total_rent_fee->setValue(0);
	    
	    $total_maintenance = new Zend_Dojo_Form_Element_NumberTextBox("total_maintenance");
	    $total_maintenance->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            'readonly'=>'readonly',
	        ));
	    $total_maintenance->setValue(0);
	    
	    $profit = new Zend_Dojo_Form_Element_NumberTextBox("profit");
	    $profit->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            'readonly'=>'readonly',
	        ));
	    $profit->setValue(0);
	    
	    $toatal_amount_fix = new Zend_Dojo_Form_Element_NumberTextBox("toatal_amount_fix");
	    $toatal_amount_fix->setAttribs(
	        array('dojoType'=>$this->number,
	            'class'=>"fullside",
	            'readonly'=>'readonly',
	        ));
	    $toatal_amount_fix->setValue(0);
	    
	    $db=new Vehicle_Model_DbTable_DbVehicle();
	    $rows_veh_typ=$db->getAllVehicleType();
	    $opt_payment = array(0=>$this->tr->translate("SELECT_VECHICLE_TYPE"),-1=>$this->tr->translate("Add Vehicle Type"));
	    $vehicle_type = new Zend_Dojo_Form_Element_FilteringSelect("vehicle_type");
	    $vehicle_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>"fullside",'autoComplete'=>'false', 'queryExpr'=>'*${0}*',
	        'onchange'=>'getPopupFormVehicleType()'
	    ));
	    foreach ($rows_veh_typ as $rs){
	        $opt_payment[$rs["id"]] = $rs["title"];
	    }
	    $vehicle_type->setMultiOptions($opt_payment);
	    
	    $vehicle_ref_no = new Zend_Dojo_Form_Element_TextBox('vehicle_ref_no');
	    $vehicle_ref_no->setAttribs(array(
	        'dojoType'=>'dijit.form.ValidationTextBox',
	        'class'=>'fullside',
	        'required'=>true
	    ));
	    
	    
	    $this->addElements(array(
	        $rent_no,
	        $customer,
	        $rent_date,
	        $phone,
	        $return_date,
	        $expired_date,
	        $address,
	        $time,
	        $return_money,
	        $paid,
	        $cost_month,
	        $payment_date,
	        $remark,
	        $total_payment,
	        $total_paid,
	        $balance,
	        $total_rent_fee,
	        $total_rent_fee,
	        $profit,
	        $total_maintenance,
	        $vehicle_type,
	        $vehicle_ref_no,
	        $color,
	        $repair_date,
	        $fix_name,
	        $toatal_amount_fix
	        
	    ));
	    return $this;
	}
	
}

