<?php 
Class Location_Form_FrmSearch extends Zend_Dojo_Form{
	protected $tr = null;
	protected $btn =null;//text validate
	protected $filter = null;
	protected $text =null;
	protected $validate = null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->btn = 'dijit.form.Button';
		$this->validate = 'dijit.form.ValidationTextBox';
	}	
	public function search(){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_title = new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>$this->text,
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")));
		$_title->setValue($request->getParam("title"));
	
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>$this->filter,
				'class'=>'fullside'));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL_STATUS"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		
		$service_rs = $db->getServiceType();
		$_service_type = new Zend_Dojo_Form_Element_FilteringSelect("service_type");
		$_arr=array(0=>$this->tr->translate("Choose Service Type"));
		if(!empty($service_rs))foreach($service_rs AS $row){
			$_arr[$row['id']]=$row['name'];
		}
		$_service_type->setMultiOptions($_arr);
		$_service_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$_service_type->setValue($request->getParam("service_type"));
		
		$location_rs = $db->getAllLocationType();
		$_location_type = new Zend_Dojo_Form_Element_FilteringSelect("location_type");
		$_arr_loca = array("-1"=>$this->tr->translate("Choose Location Type"));
		if(!empty($location_rs))foreach($location_rs AS $row){
			$_arr_loca[$row['id']]=$row['name'];
		}
		$_location_type->setMultiOptions($_arr_loca);
		$_location_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'false',
				'class'=>'fullside'));
		$_location_type->setValue($request->getParam("location_type"));
		
		
		$_driver_type = new Zend_Dojo_Form_Element_FilteringSelect('driver_type');
		$_driver_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array("-1"=>$this->tr->translate("CHOOSE_DRIVER_TYPE"));
		$_driver_rs = $db->getViewsAsName(8);
		if(!empty($_driver_rs))foreach($_driver_rs AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		
		$_driver_type->setMultiOptions($_status_opt);
		$_driver_type->setValue($request->getParam("driver_type"));
		
		$province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt = array("-1"=>$this->tr->translate("CHOOSE_PROVINCE"));
		$rss = $db->getAllProvince();
		if(!empty($rss))foreach($rss AS $row){
			$opt[$row['id']]=$row['name'];
		}
		$province->setMultiOptions($opt);
		$province->setValue($request->getParam("province"));
		
		$this->addElements(array($_service_type,$_title,$_status,$_driver_type,$province,$_location_type));
	
		return $this;
	}

}