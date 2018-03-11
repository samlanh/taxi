<?php 
Class Expense_Form_FrmVehiclemaintenance extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function search(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		$_title = new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>'dijit.form.TextBox',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")));
		$_title->setValue($request->getParam("title"));
	
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL_STATUS"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		
		$customer_type=  new Zend_Dojo_Form_Element_FilteringSelect('customer_type');
		$customer_type->setAttribs(
				array(
						'dojoType'=>'dijit.form.FilteringSelect',
						'class'=>'fullside',
				));
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_CUSTOMER_TYPE"));
		$customer_opt= $db->getViewsAsName(9);
		if(!empty($customer_opt))foreach($customer_opt AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$customer_type->setMultiOptions($_status_opt);
		$customer_type->setValue($request->getParam("customer_type"));
		
		$agencytype_id=  new Zend_Dojo_Form_Element_FilteringSelect('agencytype_id');
		$agencytype_id->setAttribs(
				array(
						'dojoType'=>'dijit.form.FilteringSelect',
						'class'=>'fullside',
				));
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_AGENCY_TYPE"));
		$customer_opt= $db->getViewsAsName(10);
		if(!empty($customer_opt))foreach($customer_opt AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$agencytype_id->setMultiOptions($_status_opt);
		$agencytype_id->setValue($request->getParam("agencytype_id"));
		
		$this->addElements(array($agencytype_id,$_title,$_status,$customer_type));
	
		return $this;
	}
	 
	public function addVehicleMaintenance($data=null){
		
		$invoice = new Zend_Dojo_Form_Element_TextBox('invoice');
		$invoice->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
	 
		$title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
		));
		
		$payment_method=  new Zend_Dojo_Form_Element_FilteringSelect('payment_method');
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_PAYMENT_TYPE"));
		$row= $expen->getViewsAsName(15);
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$payment_method->setMultiOptions($_status_opt);

		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$start_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		$start_date->setValue(date("Y-m-d"));
		
		$monthly_price =new Zend_Dojo_Form_Element_NumberTextBox('monthly_price');
		$monthly_price->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$total_amount = new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readonly'=>true
		));
		
		///////////////////////
		
		$citynormalprice = new Zend_Dojo_Form_Element_NumberTextBox('citynormalprice');
		$citynormalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		)); 
		
		$_Description = new Zend_Dojo_Form_Element_Textarea('note_de');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
				'class'=>'fullside',
				'style'=>"font-size:14px;font-family: 'Khmer OS Battambang';height:50px;"
		));
		
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
					
		));
		$options= array(1=>$this->tr->translate("ACTIVE"),2=>$this->tr->translate("DEACTIVE"));
		$_stutas->setMultiOptions($options);
		
		$vehicle_name=  new Zend_Dojo_Form_Element_FilteringSelect('vehicle_id');
		$vehicle_name->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',
				'Onchange'=>'getVehicleById();getCarentalById()'));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_VEHICLE_REF_NO"));
		$row= $expen->getVehicleRefNo();
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$vehicle_name->setMultiOptions($_status_opt);
		
		if($data!=null){
			//print_r($data);exit();
			$vehicle_name->setValue($data['vehicle_id']);
			$invoice->setValue($data['invoice']);
			$title->setValue($data['title']);
			$payment_method->setValue($data['payment_type']);
			$cheque_num->setValue($data['cheque_no']);
			$total_amount->setValue($data['total_amount']);
			$_Description->setValue($data['description']);
			$_stutas->setValue($data['status']);
		}
		$this->addElements(array($vehicle_name,$invoice,$title,$payment_method,$start_date,$monthly_price,$cheque_num,$total_amount, 
				$_Description,$_stutas ));
		return $this;
	
	}	
	
	public function frmExpens($data=null){
	
		$invoice = new Zend_Dojo_Form_Element_TextBox('invoice');
		$invoice->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
	
		$title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
		));
	
		$payment_method=  new Zend_Dojo_Form_Element_FilteringSelect('payment_method');
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_PAYMENT_TYPE"));
		$row= $expen->getViewsAsName(15);
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$payment_method->setMultiOptions($_status_opt);
	
		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$start_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		$start_date->setValue(date("Y-m-d"));
	
		$monthly_price =new Zend_Dojo_Form_Element_NumberTextBox('monthly_price');
		$monthly_price->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
	
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
	
		$total_amount = new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readonly'=>true
		));
	
		///////////////////////
	
		$citynormalprice = new Zend_Dojo_Form_Element_NumberTextBox('citynormalprice');
		$citynormalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$_Description = new Zend_Dojo_Form_Element_Textarea('note_de');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
				'class'=>'fullside',
				'style'=>"font-size:14px;font-family: 'Khmer OS Battambang';height:50px;"
		));
	
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
					
		));
		$options= array(1=>$this->tr->translate("ACTIVE"),2=>$this->tr->translate("DEACTIVE"));
		$_stutas->setMultiOptions($options);
	
		$vehicle_name=  new Zend_Dojo_Form_Element_FilteringSelect('vehicle_id');
		$vehicle_name->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',
				'Onchange'=>'getVehicleById();getCarentalById()'));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_VEHICLE_REF_NO"));
		$row= $expen->getVehicleRefNo();
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$vehicle_name->setMultiOptions($_status_opt);
	
		if($data!=null){
			//print_r($data);exit();
// 			$vehicle_name->setValue($data['vehicle_id']);
// 			$invoice->setValue($data['invoice']);
// 			$title->setValue($data['title']);
// 			$payment_method->setValue($data['payment_type']);
// 			$cheque_num->setValue($data['cheque_no']);
// 			$total_amount->setValue($data['total_amount']);
// 			$_Description->setValue($data['description']);
// 			$_stutas->setValue($data['status']);
		}
		$this->addElements(array($vehicle_name,$invoice,$title,$payment_method,$start_date,$monthly_price,$cheque_num,$total_amount,
				$_Description,$_stutas ));
		return $this;
	
	}
	
	public function frmIncome($data=null){
	
		$invoice = new Zend_Dojo_Form_Element_TextBox('invoice');
		$invoice->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
	
		$title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
		));
	
		$payment_method=  new Zend_Dojo_Form_Element_FilteringSelect('payment_method');
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_PAYMENT_TYPE"));
		$row= $expen->getViewsAsName(15);
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$payment_method->setMultiOptions($_status_opt);
	
		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$start_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		$start_date->setValue(date("Y-m-d"));
	
		$monthly_price =new Zend_Dojo_Form_Element_NumberTextBox('monthly_price');
		$monthly_price->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
	
		$cheque_num = new Zend_Dojo_Form_Element_ValidationTextBox('cheque_num');
		$cheque_num->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
	
		$total_amount = new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readonly'=>true
		));
	
		///////////////////////
	
		$citynormalprice = new Zend_Dojo_Form_Element_NumberTextBox('citynormalprice');
		$citynormalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$_Description = new Zend_Dojo_Form_Element_Textarea('note_de');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
				'class'=>'fullside',
				'style'=>"font-size:14px;font-family: 'Khmer OS Battambang';height:50px;"
		));
	
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
					
		));
		$options= array(1=>$this->tr->translate("ACTIVE"),2=>$this->tr->translate("DEACTIVE"));
		$_stutas->setMultiOptions($options);
	
		$vehicle_name=  new Zend_Dojo_Form_Element_FilteringSelect('vehicle_id');
		$vehicle_name->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',
				'Onchange'=>'getVehicleById();getCarentalById()'));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_VEHICLE_REF_NO"));
		$row= $expen->getVehicleRefNo();
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$vehicle_name->setMultiOptions($_status_opt);
	
		if($data!=null){
						$invoice->setValue($data['invoice']);
						$title->setValue($data['title']);
						$payment_method->setValue($data['payment_type']);
						$cheque_num->setValue($data['cheque_no']);
						$total_amount->setValue($data['total_amount']);
						$_Description->setValue($data['description']);
						$_stutas->setValue($data['status']);
		}
		$this->addElements(array($vehicle_name,$invoice,$title,$payment_method,$start_date,$monthly_price,$cheque_num,$total_amount,
				$_Description,$_stutas ));
		return $this;
	}
	
}