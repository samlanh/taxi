<?php
class Bookings_Form_FrmCommission extends Zend_Dojo_Form{
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
		$booking_code = $_db->getNewCommissionPaymentNO();
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
		$agency->setAttribs(array('dojoType'=>$this->filter,'class'=>"fullside",'onChange'=>'getAgent(),getCommissionAgent();'));
		foreach ($row_agen as $rs){
			$opt_agen[$rs["id"]] = $rs["name"];
		}
		$agency->setMultiOptions($opt_agen);
		
		
		
		
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
		
		$balance = new Zend_Dojo_Form_Element_NumberTextBox("balance");
		$balance->setAttribs(
				array('dojoType'=>$this->number,
						'class'=>"fullside",
						'readonly'=>'readonly',
				));
		$balance->setValue(0);
		
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
		
		if (!empty($data)){
			
			$_reciept_no->setValue($data['payment_no']);
			$agency->setValue($data['agency_id']);
			$payment_date->setValue(date("Y-m-d",strtotime($data['payment_date'])));
			$remark->setValue($data['note']);
			$_amount->setValue($data['amount']);
			$payment_method->setValue($data['payment_method']);
// 			$total_payment->setValue($data['booking_no']);
			$balance->setValue($data['balance']);
			$total_paid->setValue($data['paid']);
			$total_due->setValue($data['total_due']);
			
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
				
				$booking_date_start,
				$booking_date_end
			));
		return $this;
	}
	
}

