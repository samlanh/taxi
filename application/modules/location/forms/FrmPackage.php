<?php 
Class Location_Form_FrmPackage extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddPackage($data=null){
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_location_name = new Zend_Dojo_Form_Element_ValidationTextBox('location_name');
		$_location_name->setAttribs(array('dojoType'=>'dijit.form.ValidationTextBox',
				'required'=>'true','missingMessage'=>'Invalid Module!','class'=>'fullside'
				));
		
		$_Province = new Zend_Dojo_Form_Element_FilteringSelect("province_name");
		$_Province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'Onchange'=>'getNewLocation();',
				'class'=>'fullside'));
		$option = array(0=>$this->tr->translate("Choose Province"));
		$result = $db->getAllProvince();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$_Province->setMultiOptions($option);
		
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
			$_status->setValue($data['status']);
			$note->setValue($data['note']);
		}
		$this->addElements(array($_location_name,$_Province,$_status,$note));
		return $this;
		
	}
}