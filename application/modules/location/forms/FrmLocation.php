<?php 
Class Location_Form_FrmLocation extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddLocation($data=null){
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_location_name = new Zend_Dojo_Form_Element_ValidationTextBox('location_name');
		$_location_name->setAttribs(array('dojoType'=>'dijit.form.ValidationTextBox',
				'required'=>'true','missingMessage'=>'Invalid Module!','class'=>'fullside'
				));
		
		$_Province = new Zend_Dojo_Form_Element_FilteringSelect("province_name");
		$_Province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$option = array();
		$result = $db->getAllProvince();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$_Province->setMultiOptions($option);
		$_Province->setValue(25);
		
// 		$service_rs = $db->getServiceType();
// 		$_service_type = new Zend_Dojo_Form_Element_FilteringSelect("service_type");
// 		$_arr=array();
// 		if(!empty($service_rs))foreach($service_rs AS $row){
// 			$_arr[$row['id']]=$row['name'];
// 		}
// 		$_service_type->setMultiOptions($_arr);
// 		$_service_type->setAttribs(array(
// 				'dojoType'=>'dijit.form.FilteringSelect',
// 				'required'=>'true',
// 				'Onchange'=>'checkLocationType();',
// 				'class'=>'fullside'));
		
// 		$location_rs = $db->getAllLocationType();
// 		$_location_type = new Zend_Dojo_Form_Element_FilteringSelect("location_type");
// 		$_arr_loca=array();
// 		if(!empty($location_rs))foreach($location_rs AS $row){
// 			$_arr_loca[$row['id']]=$row['name'];
// 		}
// 		$_location_type->setMultiOptions($_arr_loca);
// 		$_location_type->setAttribs(array(
// 				'dojoType'=>'dijit.form.FilteringSelect',
// 				'required'=>'true',
// 				'readonly'=>true,
// 				'class'=>'fullside'));
		
		$rs_status = $db->getVewOptoinTypeByType(2);
		$_status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$_arr_sta=array(
		);
		if(!empty($rs_status))foreach($rs_status AS $row){
			$_arr_sta[$row['key_code']]=$row['name_en'];
		}
		$_status->setMultiOptions($_arr_sta);
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		
		$note = new Zend_Dojo_Form_Element_Textarea('note');
		$note->setAttribs(array('dojoType'=>'dijit.form.Textarea','class'=>'ckeditor',
				'style'=>'height:300px !important;'));
		
		if(!empty($data)){
			$_location_name->setValue($data['location_name']);
			$_Province->setValue($data['province_id']);
// 			$_service_type->setValue($data['service_type']);
// 			$_location_type->setValue($data['locationtype_id']);
			$_status->setValue($data['status']);
			$note->setValue($data['note']);
		}
		$this->addElements(array($_location_name,$_Province,
// 				$_service_type,$_location_type, 
				$_status,$note));
		return $this;
		
	}
	public function FrmAddLocationType($data=null){
		$db = new Application_Model_DbTable_DbGlobal();
	
		$_loation_type = new Zend_Dojo_Form_Element_ValidationTextBox('loation_type');
		$_loation_type->setAttribs(array('dojoType'=>'dijit.form.ValidationTextBox',
				'required'=>'true','missingMessage'=>'Invalid Module!','class'=>'fullside'
		));
	
		$rs_status = $db->getVewOptoinTypeByType(2);
		$_status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$_arr_sta=array(
		);
		if(!empty($rs_status))foreach($rs_status AS $row){
			$_arr_sta[$row['key_code']]=$row['name_en'];
		}
		$_status->setMultiOptions($_arr_sta);
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
	
		if(!empty($data)){
			$_loation_type->setValue($data['title']);
			$_status->setValue($data['status']);
		}
		$this->addElements(array($_loation_type, $_status));
		return $this;
	}
	
}