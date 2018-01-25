<?php 
Class Expense_Form_FrmSearchInfo extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function search(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		$_title = new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")));
		$_title->setValue($request->getParam("title"));
	
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
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
		
		$_c_type=  new Zend_Dojo_Form_Element_FilteringSelect('c_type');
		$_c_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				''=>$this->tr->translate("CHOOSE_EXPENSE_TYPE"),
				1=>$this->tr->translate("IS_VEHICLE_MAINTENANCE"),
				2=>$this->tr->translate("IS_EXPENSE"),
				);
		$_c_type->setMultiOptions($_status_opt);
		$_c_type->setValue($request->getParam("c_type"));
		
		
		$_releasedate = new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$_releasedate->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'onchange'=>'CalculateDate();',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside'));
		$_date = $request->getParam("start_date");
		
		if(!empty($_date)){
			$_releasedate->setValue($_date);
		}
		
		
		
		$_dateline = new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$_dateline->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'required'=>'true','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		$_date = $request->getParam("end_date");
		if(empty($_date)){
			$_date = date("Y-m-d");
		}
		$_dateline->setValue($_date);
		
		$payment_method=  new Zend_Dojo_Form_Element_FilteringSelect('payment_method');
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$expen=new Expense_Model_DbTable_DbVehicleMaintenance();
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_PAYMENT_TYPE"));
		$row= $expen->getViewsAsName(15);
		if(!empty($row))foreach($row AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$payment_method->setMultiOptions($_status_opt);
		$payment_method->setValue($request->getParam("payment_method"));
		
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
		$vehicle_name->setValue($request->getParam("vehicle_id"));
		
		$this->addElements(array($vehicle_name,$payment_method,$_dateline,$_releasedate,$_c_type,$agencytype_id,$_title,$_status,$customer_type));
	
		return $this;
	}
	 
}