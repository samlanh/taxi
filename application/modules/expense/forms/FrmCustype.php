<?php 
Class Agency_Form_FrmCustype extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function search(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
	
		$_title = new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>'dijit.form.TextBox',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")));
		$_title->setValue($request->getParam("title"));
	
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL_STATUS"),
				"1"=>$this->tr->translate("ACTIVE"),
				"0"=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		$this->addElements(array($_title,$_status));
	
		return $this;
	}
	public function FrmAddCustomerType($data=null){
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array();
		$status= $db->getViews(2);
		if(!empty($status))foreach($status AS $row){
			$_status_opt[$row['key_code']]=$row['name_en'];
		}
		$_status->setMultiOptions($_status_opt);
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("TITLE"))
				);
		if($data!=null){
			$_title->setValue($data['name_en']);
			$_status->setValue($data['status']);
		}
		$this->addElements(array($_title,$_status));
		return $this;
		
	}
}