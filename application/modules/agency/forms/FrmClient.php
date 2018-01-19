<?php 
Class Agency_Form_FrmClient extends Zend_Dojo_Form {
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
	public function FrmAddClient($data=null){
		
		$_dob= new Zend_Dojo_Form_Element_DateTextBox('dob_client');
		$_dob->setValue(date("Y-m-d"));
		$_dob->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_namekh = new Zend_Dojo_Form_Element_TextBox('name_kh');
		$_namekh->setAttribs(array(
						'dojoType'=>'dijit.form.ValidationTextBox',
						'class'=>'fullside',
						'required' =>'true'
		));
		
		$_clientno = new Zend_Dojo_Form_Element_TextBox('client_no');
		$_clientno->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'style'=>'color:red;',
				'readOnly'=>true
		));
		$id_client = $db->getNewClientId();
		$_clientno->setValue($id_client);
	
		$_nameen = new Zend_Dojo_Form_Element_ValidationTextBox('name_en');
		$_nameen->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$_sex = new Zend_Dojo_Form_Element_FilteringSelect('sex');
		$_sex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_status = $db->getVewOptoinTypeByType(1,1);
		unset($opt_status[-1]);
		unset($opt_status['']);
		$_sex->setMultiOptions($opt_status);
		
		$_situ_status = new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_situ_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		
		$group_num = new Zend_Dojo_Form_Element_TextBox('group_num');
		$group_num->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$country = new Zend_Dojo_Form_Element_TextBox('country');
		$country->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$commune = new Zend_Dojo_Form_Element_TextBox('commune');
		$commune->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$district = new Zend_Dojo_Form_Element_TextBox('district');
		$district->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt = $db->getAllProvince(1);
		$province->setMultiOptions($opt);
		
		$_street = new Zend_Dojo_Form_Element_TextBox('street');
		$_street->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$_id_type = new Zend_Dojo_Form_Element_FilteringSelect('id_type');
		$_id_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$address = new Zend_Dojo_Form_Element_TextBox('address');
		$address->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$photo=new Zend_Form_Element_File('photo');
		$photo->setAttribs(array(
		));
		
		$job = new Zend_Dojo_Form_Element_TextBox('job');
		$job->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$national_id=new Zend_Dojo_Form_Element_TextBox('national_id');
		$national_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				));
		
		$email=new Zend_Dojo_Form_Element_TextBox('email');
		$email->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$fax=new Zend_Dojo_Form_Element_TextBox('fax');
		$fax->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		

		$balance=new Zend_Dojo_Form_Element_TextBox('balance');
		$balance->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));

		$_id = new Zend_Form_Element_Hidden("id");
		
		$_desc = new Zend_Dojo_Form_Element_TextBox('desc');
		$_desc->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'min-height:30px;'));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		
		$_title=  new Zend_Dojo_Form_Element_FilteringSelect('title');
		$_title->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("Mr"),
				2=>$this->tr->translate("Mrs"),
				3=>$this->tr->translate("Miss"),
				4=>$this->tr->translate("Ms"),
				5=>$this->tr->translate("Sir"),
				6=>$this->tr->translate("Madam"),
				7=>$this->tr->translate("Dr"),
				8=>$this->tr->translate("Prof"),
				9=>$this->tr->translate("Rev"),
				10=>$this->tr->translate("Saint"),
				
		);
		$_title->setMultiOptions($_status_opt);
		
		$customer_type=  new Zend_Dojo_Form_Element_FilteringSelect('agency_type');
		$customer_type->setAttribs(
			array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onChange'=>'getPopFormCus();',
				));
		$_status_opt = array("0"=>"","-1"=>$this->tr->translate("ADD_NEW"));
		$customer_opt= $db->getViewsAsName(10);
		if(!empty($customer_opt))foreach($customer_opt AS $row){
			$_status_opt[$row['id']]=$row['name'];
		}
		$customer_type->setMultiOptions($_status_opt);
		
		$nationality = new Zend_Dojo_Form_Element_TextBox('nationality');
		$nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$company_name = new Zend_Dojo_Form_Element_TextBox('company_name');
		$company_name->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$passport = new Zend_Dojo_Form_Element_TextBox('passport');
		$passport->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$p_issuedate = new Zend_Dojo_Form_Element_DateTextBox('pissue_date');
		$p_issuedate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$p_issuedate->setValue(date("Y-m-d"));
		
		$p_expireddate = new Zend_Dojo_Form_Element_DateTextBox('pexpired_date');
		$p_expireddate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$p_expireddate->setValue(date("Y-m-d"));
		
		$card_code = new Zend_Dojo_Form_Element_TextBox('card_code');
		$card_code->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$c_issuedate = new Zend_Dojo_Form_Element_DateTextBox('cissue_date');
		$c_issuedate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$c_issuedate->setValue(date("Y-m-d"));
		
		$c_expireddate = new Zend_Dojo_Form_Element_DateTextBox('cexpired_date');
		$c_expireddate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$c_expireddate->setValue(date("Y-m-d"));
		
		$ftb = new Zend_Dojo_Form_Element_TextBox('ftb');
		$ftb->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$f_issuedate = new Zend_Dojo_Form_Element_DateTextBox('fissue_date');
		$f_issuedate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$f_issuedate->setValue(date("Y-m-d"));
		
		$f_expireddate = new Zend_Dojo_Form_Element_DateTextBox('fexpired_date');
		$f_expireddate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$f_expireddate->setValue(date("Y-m-d"));
		
		$address1 = new Zend_Dojo_Form_Element_TextBox('address1');
		$address1->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$address2 = new Zend_Dojo_Form_Element_TextBox('address2');
		$address2->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$i_city = new Zend_Dojo_Form_Element_TextBox('i_city');
		$i_city->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$in_zipcode = new Zend_Dojo_Form_Element_TextBox('i_zipcode');
		$in_zipcode->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$state = new Zend_Dojo_Form_Element_TextBox('state');
		$state->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$in_phone = new Zend_Dojo_Form_Element_TextBox('i_phone');
		$in_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		
		$icontry = new Zend_Dojo_Form_Element_FilteringSelect('countries');
		$icontry->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$row = $db->getAllCountry();
		$opt_country = array();
		if(!empty($row)){
			foreach ($row as $rs){
				$opt_country[$rs['id']]=$rs['country_name'];
			}
		}
		$icontry->setMultiOptions($opt_country);
		$id = new Zend_Dojo_Form_Element_TextBox('id');
		$id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$occupation = new Zend_Dojo_Form_Element_TextBox('occupation');
		$occupation->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$password = new Zend_Dojo_Form_Element_PasswordTextBox('password');
		$password->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$email_login=new Zend_Dojo_Form_Element_TextBox('email_login');
		$email_login->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$old_photo = new Zend_Form_Element_Hidden('old_photo');
		
		
		if($data!=null){
			$occupation->setValue($data['occupation']);
			$old_photo->setValue($data['photo']);
			$id->setValue($data['id']);
			$_title->setValue($data['title']);
			$_namekh->setValue($data['first_name']);
			$_nameen->setValue($data['last_name']);
			$_sex->setValue($data['sex']);
			$_dob->setValue($data['dob']);
			$country->setValue($data['pob']);
			$nationality->setValue($data['nationality']);
			$company_name->setValue($data['company_name']);
			$customer_type->setValue($data['customer_type']);
			$_desc->setValue($data['note']);
			$passport->setValue($data['passport_name']);
			
			$passport->setValue($data['passport_name']);
			$passport->setValue($data['passport_name']);
			$p_issuedate->setValue($data['pass_issuedate']);
			$p_expireddate->setValue($data['pass_expireddate']);
			
			$card_code->setValue($data['card_name']);
			$c_issuedate->setValue($data['card_issuedate']);
			$c_expireddate->setValue($data['card_expireddate']);
			
			$ftb->setValue($data['ftb']);
			$f_issuedate->setValue($data['ftb_issuedate']);
			$f_expireddate->setValue($data['ftb_expireddate']);
				
			$_phone->setValue($data['phone']);
			$email->setValue($data['email']);
			$fax->setValue($data['fax']);
			$group_num->setValue($data['group_num']);
			$address->setValue($data['house_num']);
			$_street->setValue($data['street']);
			$commune->setValue($data['commune']);
			$province->setValue($data['province_id']);
$district->setValue($data['district']);
			$balance->setValue($data['balance']);	
			
			$address1->setValue($data['address1']);
			$address2->setValue($data['address2']);
			$state->setValue($data['i_state']);
			$i_city->setValue($data['i_city']);
			$in_zipcode->setValue($data['i_zipcode']);
            $in_phone->setValue($data['i_phone']);
			$icontry->setValue($data['country']);
			$_situ_status->setValue($data['status']);
			$email_login->setValue($data['email_login']);
		}
		$this->addElements(array($state,$occupation,$old_photo,$id,$icontry,$in_phone,$in_zipcode,$i_city,$address2,$address1,$customer_type,$commune,$district,$province,$p_issuedate,$p_expireddate,$c_issuedate,$c_expireddate,$f_issuedate,$f_expireddate,$passport,$card_code,$ftb,$company_name,$nationality,$_title,$balance,$fax,$email,$group_num,$country,$_id,$photo,$job,$national_id,$_namekh,$_nameen,$_sex,$_situ_status,
				$_street,$_id_type,$address,$_phone,$_desc,$_status,$_clientno,$_dob,
				$password,$email_login
				
				));
		return $this;
		
	}
	public function FrmaddGuide($data=null){
	 
        $monthly_price =new Zend_Dojo_Form_Element_NumberTextBox('monthly_price');
		$monthly_price->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		$pob= new Zend_Dojo_Form_Element_TextBox('pob');
		$pob->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'
		));
		$att_file= new Zend_Form_Element_File('att_file');
		$att_file->setAttribs(array('class'=>'fullside'
		));

		$_dob= new Zend_Dojo_Form_Element_DateTextBox('dob_client');
		$_dob->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		$_dob->setValue(date("Y-m-d"));
	
	
		$nationality = new Zend_Dojo_Form_Element_TextBox('nationality');
		$nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
	
		$request=Zend_Controller_Front::getInstance()->getRequest();
	
		$_email = new Zend_Dojo_Form_Element_TextBox('email');
		$_email->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$db = new Application_Model_DbTable_DbGlobal();
	
		$_namekh = new Zend_Dojo_Form_Element_TextBox('name_kh');
		$_namekh->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
	
		$id_client = $db->getDriverCode();
		$_clientno = new Zend_Dojo_Form_Element_TextBox('client_no');
		$_clientno->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'readonly'=>'readonly',
				'style'=>'color:red;'
		));
		$_clientno->setValue($id_client);
	
		$_nameen = new Zend_Dojo_Form_Element_ValidationTextBox('name_en');
		$_nameen->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
	
		$_sex = new Zend_Dojo_Form_Element_FilteringSelect('sex');
		$_sex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_status = $db->getVewOptoinTypeByType(1,1);
		unset($opt_status[-1]);
		unset($opt_status['']);
		$_sex->setMultiOptions($opt_status);
	
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$normalprice = new Zend_Dojo_Form_Element_NumberTextBox('cnormalprice');
		$normalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$otprice = new Zend_Dojo_Form_Element_NumberTextBox('cotprice');
		$otprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$pnormalprice = new Zend_Dojo_Form_Element_NumberTextBox('pnormalprice');
		$pnormalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$potprice = new Zend_Dojo_Form_Element_NumberTextBox('potprice');
		$potprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$photo=new Zend_Form_Element_File('photo');
		$photo->setAttribs(array(
		));
		$national_id=new Zend_Dojo_Form_Element_TextBox('national_id');
		$national_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$experience=new Zend_Dojo_Form_Element_TextBox('experience');
		$experience->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$publicholiday=new Zend_Dojo_Form_Element_NumberTextBox('poblicholiday_price');
		$publicholiday->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$weekend_price=new Zend_Dojo_Form_Element_NumberTextBox('weekend_price');
		$weekend_price->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$experience_num=new Zend_Dojo_Form_Element_NumberTextBox('experience_number');
		$experience_num->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$p_popublicholiday=new Zend_Dojo_Form_Element_NumberTextBox('ppoblicholiday_price');
		$p_popublicholiday->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$p_weekend=new Zend_Dojo_Form_Element_NumberTextBox('pweekend_price');
		$p_weekend->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
	
		$_id = new Zend_Form_Element_Hidden("id");
		
		$_desc = new Zend_Dojo_Form_Element_TextBox('desc');
		$_desc->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'min-height:30px;'));
		
		$lang = new Zend_Dojo_Form_Element_TextBox('lang');
		$lang->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:96%;min-height:50px;'));
	
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$opt = array(1=>"Active",0=>"Deactive");
		$_status->setMultiOptions($opt);
		
		$_type = new Zend_Dojo_Form_Element_FilteringSelect('type');
		$_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
// 		$_status_opt = array(
// 				1=>$this->tr->translate("Guide"),
// 				2=>$this->tr->translate("Driver"),
// 				3=>$this->tr->translate("Both")
// 		);
		$_status_opt = array();
	    $_status_opt = $db->getVewOptoinTypeByType(8,1,null,1);	
		$_type->setMultiOptions($_status_opt);
		
		$address = new Zend_Dojo_Form_Element_TextBox('home');
		$address->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$groupnum = new Zend_Dojo_Form_Element_TextBox('group');
		$groupnum->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$streetnum = new Zend_Dojo_Form_Element_TextBox('street');
		$streetnum->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$commune = new Zend_Dojo_Form_Element_TextBox('commune');
		$commune->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$district = new Zend_Dojo_Form_Element_TextBox('district');
		$district->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt = $db->getAllProvince(1);
		$province->setMultiOptions($opt);
		
		$id_card = new Zend_Dojo_Form_Element_TextBox('id_card');
		$id_card->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$issued_date = new Zend_Dojo_Form_Element_DateTextBox('issued_date');
		$issued_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$issued_date->setValue(date("Y-m-d"));
		
		$registered_date = new Zend_Dojo_Form_Element_DateTextBox('registered_date');
		$registered_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$registered_date->setValue(date("Y-m-d"));
		
		$expired_date = new Zend_Dojo_Form_Element_DateTextBox('expired_date');
		$expired_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$expired_date->setValue(date("Y-m-d"));
		
		$_email = new Zend_Dojo_Form_Element_TextBox('email');
		$_email->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'style'=>'color:red;'
		));
		
		///////////////////////
		
		$citynormalprice = new Zend_Dojo_Form_Element_NumberTextBox('citynormalprice');
		$citynormalprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		
		
		$cityweekendprice = new Zend_Dojo_Form_Element_NumberTextBox('cityweekendprice');
		$cityweekendprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$cityotprice = new Zend_Dojo_Form_Element_NumberTextBox('cityotprice');
		$cityotprice->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$citypublic = new Zend_Dojo_Form_Element_NumberTextBox('citypublicprice');
		$citypublic->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$_id_no = new Zend_Form_Element_Hidden('id');
		
		if($data!=null){
			$_id_no->setValue($data['id']);
			$_clientno->setValue($data['driver_id']);
			$_namekh->setValue($data['first_name']);
			$_nameen->setValue($data['last_name']);
			$_sex->setValue($data['sex']);
			$_dob->setValue($data['dob']);
			$pob->setValue($data['pob']);
			$nationality->setValue($data['nationality']);
			$national_id->setValue($data['doc_number']);
			$_desc->setValue($data['lang_note']);
			$_type->setValue($data['position_type']);
			$id_card->setValue($data['id_card']);
			$issued_date->setValue($data['issue_date']);
			$expired_date->setValue($data['expired_date']);
			$registered_date->setValue($data['register_date']);
			$experience->setValue($data['experience_desc']);
			$_phone->setValue($data['tel']);
			$_email->setValue($data['email']);
			$groupnum->setValue($data['group_num']);
			$address->setValue($data['home_num']);
			$streetnum->setValue($data['street']);	
			$commune->setValue($data['commune']);
			$district->setValue($data['district']);
			$province->setValue($data['province_id']);
			
			$pnormalprice->setValue($data['p_normalprice']);
			$p_weekend->setValue($data['p_weekendprice']);
			$p_popublicholiday->setValue($data['p_holidayprice']);
			$potprice->setValue($data['p_otprice']);

			$normalprice->setValue($data['c_normalprice']);
			$weekend_price->setValue($data['c_weekendprice']);
			$publicholiday->setValue($data['c_holidayprice']);
			$otprice->setValue($data['c_otprice']);
			
			$citypublic->setValue($data['citypublicprice']);
			$cityweekendprice->setValue($data['cityweekendprice']);
			$cityotprice->setValue($data['cityotprice']);
			$citynormalprice->setValue($data['citynormalprice']);
			$_status->setValue($data['status']);
                        $monthly_price->setValue($data["monthly_price"]);
		}
		$this->addElements(array($monthly_price,$citypublic,$cityotprice,$citynormalprice,$cityweekendprice,$province,$expired_date,$issued_date,$registered_date,$id_card,$district,$commune,$streetnum,$groupnum,$p_popublicholiday,$p_weekend,$experience,$publicholiday,$weekend_price,$pnormalprice,$potprice,$normalprice,$otprice,$lang,$address,$_type,$nationality,$_id,$photo,$national_id,
				$_email,$_namekh,$_nameen,$_sex,$_id_no,
				$_phone,$_desc,$_status,$_clientno,$_dob,$att_file,$pob));
		return $this;
	
	}	
}