<?php
class Bookings_Form_FrmDriverPaymentNew extends Zend_Dojo_Form{
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
		$booking_code = $_db->getCearDriverPaymentNO();
		$_reciept_no = new Zend_Dojo_Form_Element_ValidationTextBox('reciept_no');
		$_reciept_no->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
				'readonly'=>true,
				'style'=>'color:red',
				'placeholder'=>$this->tr->translate("RECIEPT_NO")
		));
		$_reciept_no->setValue($booking_code);
		
		$c_date = date("Y-m-d");
		$payment_date= new Zend_Dojo_Form_Element_DateTextBox("payment_date");
		$payment_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",'readonly'=>'readonly'
				));
		$payment_date->setValue($c_date);
		
		
		$row_agen = $_db->getAllAgency();
		$opt_agen = array(0=>$this->tr->translate("SELECT_AGENCY"));
		$agency = new Zend_Dojo_Form_Element_FilteringSelect("agency");
		$agency->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAllAgentcyPayment(2);'));//getAllAgentcyPayment()
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency->setMultiOptions($opt_agen);
		
		$row_dri = $_db->getAllDriver();
		$opt_dri = array(0=>$this->tr->translate("SELECT_DRIVER"));
		$driver = new Zend_Dojo_Form_Element_FilteringSelect("driver");
		$driver->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'onChange'=>'getAllAgentcyPayment(2);getDriverInfoByid()'));
		foreach ($row_dri as $rs){
			$opt_dri[$rs["id"]] = $rs["name"];
		}
		$driver->setMultiOptions($opt_dri);
		
		$remark = new Zend_Dojo_Form_Element_TextBox("remark");
		$remark->setAttribs(array('dojoType'=>$this->textareas,'class'=>"fullside",));
		
		$row_payment = $_db->getVewOptoinTypeByTypes(11);
		$opt_payment = array(0=>$this->tr->translate("SELECT_PAYMENT_METHOD"));
		$payment_method = new Zend_Dojo_Form_Element_FilteringSelect("payment_method");
		$payment_method->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_payment as $rs){
			$opt_payment[$rs["id"]] = $rs["name"];
		}
		$payment_method->setMultiOptions($opt_payment);
		
		$opt_payment = array(1=>$this->tr->translate("BOOKING_NO"),2=>$this->tr->translate("DRIVER_NAME"));
		$payment_by = new Zend_Dojo_Form_Element_FilteringSelect("payment_by");
		$payment_by->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getPaymentBy()'));
		$payment_by->setMultiOptions($opt_payment);
		
		
		
		$booking_nos = $_db->getAllDriverBookingNo();
		$opt_b = array(0=>$this->tr->translate("SELECT_BOOKING_NO"));
		$invoice= new Zend_Dojo_Form_Element_FilteringSelect("invoice");
		$invoice->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAllAgentcyPayment(1)',
				));
		foreach ($booking_nos as $rs){
			$opt_b[$rs["id"]] = $rs["name"];
		}
		$invoice->setMultiOptions($opt_b);
		
		
		$_amount = new Zend_Dojo_Form_Element_NumberTextBox('amount');
		$_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
				'onKeyup'=>'checkAmout()',
				'placeholder'=>$this->tr->translate("AMOUNT")
		));
		$_amount->setValue(0);
		
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
						'readonly'=>'readonly',
				));
		$total_paid->setValue(0);
		
		$total_commission = new Zend_Dojo_Form_Element_NumberTextBox("total_commission_fee");
		$total_commission->setAttribs(
				array('dojoType'=>$this->number,
						'readonly'=>'readonly',
						'class'=>"fullside",
				));
		$total_commission->setValue(0);
		
		$total_paid_commission = new Zend_Dojo_Form_Element_NumberTextBox("total_paid_commission");
		$total_paid_commission->setAttribs(
				array('dojoType'=>$this->number,
				));
		$total_paid_commission->setValue(0);
		
		$paid_commission = new Zend_Dojo_Form_Element_NumberTextBox("paid_commission");
		$paid_commission->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$paid_commission->setValue(0);
		
		$total_alls = new Zend_Dojo_Form_Element_NumberTextBox("total_alls");
		$total_alls->setAttribs(
				array(  'dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_alls->setValue(0);
		
		$profit= new Zend_Dojo_Form_Element_NumberTextBox("profit");
		$profit->setAttribs(
				array(  'dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$profit->setValue(0);
		
		$total_comission= new Zend_Dojo_Form_Element_NumberTextBox("total_comission");
		$total_comission->setAttribs(
				array(  'dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_comission->setValue(0);
		
		$total_all_paid = new Zend_Dojo_Form_Element_NumberTextBox("total_all_paid");
		$total_all_paid->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_all_paid->setValue(0);
		
		$balance = new Zend_Dojo_Form_Element_NumberTextBox("balance");
		$balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$balance->setValue(0);
		
		$all_balance = new Zend_Dojo_Form_Element_NumberTextBox("all_balance");
		$all_balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$all_balance->setValue(0);
		
		$all_agentcy = new Zend_Dojo_Form_Element_NumberTextBox("all_agentcy");
		$all_agentcy->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$all_agentcy->setValue(0);
		
		$total_owner = new Zend_Dojo_Form_Element_NumberTextBox("total_owner");
		$total_owner->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_owner->setValue(0);
		
		$total_due = new Zend_Dojo_Form_Element_NumberTextBox("total_due");
		$total_due->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_due->setValue(0);
		
		$c_date = date("Y-m-d",strtotime("-7 day"));
		$booking_date_start= new Zend_Dojo_Form_Element_DateTextBox("booking_date_start");
		$booking_date_start->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
		));
		$booking_date_start->setValue($c_date);
		
		$c_date = date("Y-m-d");
		$booking_date_end= new Zend_Dojo_Form_Element_DateTextBox("booking_date_end");
		$booking_date_end->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
		));
		$booking_date_end->setValue($c_date);
		
		$agency_paid = new Zend_Dojo_Form_Element_NumberTextBox("agency_paid");
		$agency_paid->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$agency_paid->setValue(0);
		
		$total_agen_recived = new Zend_Dojo_Form_Element_NumberTextBox("total_agen_recived");
		$total_agen_recived->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$total_agen_recived->setValue(0);
		
		$paid_agen = new Zend_Dojo_Form_Element_NumberTextBox("paid_agen");
		$paid_agen->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						//'readonly'=>'readonly',
		                'onKeyup'=>'calCulatePrice()'
				));
		 
		
		$agency_balance = new Zend_Dojo_Form_Element_NumberTextBox("agency_balance");
		$agency_balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$agency_balance->setValue(0);
		
		$total_driver_paid = new Zend_Dojo_Form_Element_NumberTextBox("total_driver_paid");
		$total_driver_paid->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						//'readonly'=>'readonly',
				));
		$total_driver_paid->setValue(0);
		$driver_balance = new Zend_Dojo_Form_Element_NumberTextBox("driver_balance");
		$driver_balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$driver_balance->setValue(0);
		
		$row_type = $_db->getVewOptoinTypeByTypes(12);
		$opt_payment = array();
		$paid_type = new Zend_Dojo_Form_Element_FilteringSelect("paid_type");
		$paid_type->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_type as $rs){
			$opt_payment[$rs["id"]] = $rs["name"];
		}
		$paid_type->setMultiOptions($opt_payment);
		
		$row_status = $_db->getVewOptoinTypeByTypes(2);
		$opt_status = array();
		$status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$status->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",));
		foreach ($row_status as $rs){
			$opt_status[$rs["id"]] = $rs["name"];
		}
		$status->setMultiOptions($opt_status);
		
		$f_date = date("Y-m-d");
		$fil_start_date= new Zend_Dojo_Form_Element_DateTextBox("fil_start_date");
		$fil_start_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
		));
		//$fil_start_date->setValue($f_date);
		
		$fil_end_date= new Zend_Dojo_Form_Element_DateTextBox("fil_end_date");
		$fil_end_date->setAttribs(array('dojoType'=>$this->date,'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>"fullside",
		));
		$fil_end_date->setValue($f_date);
		
		if (!empty($data)){
			 
			$_reciept_no->setValue($data['payment_no']);
			$payment_by->setValue($data['payment_method']);
			$_reciept_no->setValue($data['payment_no']);
			$invoice->setValue($data['booking_id']);
			$driver->setValue($data['driver_id']);
			$payment_date->setValue($data['payment_date']);
			$payment_method->setValue($data['payment_type']);
			$remark->setValue($data['note']);
			$status->setValue($data['status']);
			$total_commission->setValue($data['total_driver_fee']);
			$total_agen_recived->setValue($data['total_driver_recived']);
			$paid_agen->setValue($data['paid_driver']);
			$total_alls->setValue($data['total_alls']);
			$profit->setValue($data['total_profit']);
			$agency_paid->setValue($data['driver_paid']);
			
			$fil_start_date->setValue($data['fil_start_date']);
			$fil_end_date->setValue($data['fil_end_date']);
			$fil_start_date->setAttribs(array('readonly'=>'readonly',));
			$fil_end_date->setAttribs(array('readonly'=>'readonly',));
			$driver->setAttribs(array('readonly'=>'readonly',));
			$invoice->setAttribs(array('readonly'=>'readonly',));
			$payment_by->setAttribs(array('readonly'=>'readonly',));
			 
		}
		
		$this->addElements(array(
				$_reciept_no,
				$agency,
				$payment_date,
				$remark,
				$_amount,
				$payment_method,
				$total_payment,
				$balance,
				$total_paid,
				$total_due,
				$total_commission,
				$booking_date_start,
				$booking_date_end,
				$total_all_paid,
				$total_alls,
				$all_balance,
				$all_agentcy,
				$total_owner,
				$paid_commission,
				$total_paid_commission,
				$agency_paid,
				$agency_balance,
				$total_driver_paid,
				$driver_balance,
				$total_agen_recived,
				$paid_type,
				$paid_agen,
				$status,
				$payment_by,
				$invoice,
				$driver,
				$profit,
				$total_comission,
				$fil_start_date,
				$fil_end_date,
			));
		return $this;
	}
	
}

