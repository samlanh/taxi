<?php

class Driverguide_Model_DbTable_DbDriver extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_driver';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    
    }
    function getAllDriverGuide($search=null){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$arrayview = array(1=>"name_en",2=>"name_kh");
    	$sql = "SELECT id,driver_id,first_name,last_name,
    	(SELECT ".$arrayview[$lang]." FROM `ldc_view` WHERE TYPE=1 AND key_code =$this->_name.`sex`) AS sex ,
    	(SELECT ".$arrayview[$lang]." FROM `ldc_view` WHERE TYPE=8 AND key_code =$this->_name.`position_type`) AS position_type ,
    	tel,dob,pob,nationality,
    	group_num,home_num,street,commune,district,
    	(SELECT ".$array[$lang]." FROM `ldc_province` WHERE `ldc_province`.id=province_id LIMIT 1) AS province_name,
    	status
    	FROM $this->_name  ";
    	$where = ' WHERE 1 ';
    	if($search['status_search']>-1){
    		$where.= " AND $this->_name.`status`= ".$search['status_search'];
    	}
    	if($search['driver_type']>-1){
    		$where.= " AND $this->_name.`position_type`= ".$search['driver_type'];
    	}
    	if($search['province']>-1){
    		$where.= " AND $this->_name.`province_id`= ".$search['province'];
    	}
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['title']));
    		$s_where[]=" driver_id LIKE '%{$s_search}%'";
    		$s_where[]=" first_name LIKE '%{$s_search}%'";
    		$s_where[]=" last_name LIKE '%{$s_search}%'";
    		$s_where[]=" tel LIKE '%{$s_search}%'";
    		$s_where[]=" pob LIKE '%{$s_search}%'";
    		$s_where[]=" nationality LIKE '%{$s_search}%'";
    		$s_where[]=" group_num LIKE '%{$s_search}%'";
    		$s_where[]=" home_num LIKE '%{$s_search}%'";
    		$s_where[]=" street LIKE '%{$s_search}%'";
    		$s_where[]=" commune LIKE '%{$s_search}%'";
    		$s_where[]=" district LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$where.$order);
    }
    function addDriver($_data){
    			$adapter = new Zend_File_Transfer_Adapter_Http();
    			$part= PUBLIC_PATH.'/images/driverphoto';
    			$adapter->setDestination($part);
    			$adapter->receive();
    			
    			$photo = $adapter->getFileInfo();
    			if(!empty($photo['photo']['name'])){
    				$_data['photo']=$photo['photo']['name'];
    			}else{
    				$_data['photo']='';
    			}
    			if(!empty($photo['att_file']['name'])){
    				$_data['att_file']=$photo['att_file']['name'];
    			}else{
    				$_data['att_file']="";
    			}
    			$_arr = array(
	    			'driver_id'=>$_data['client_no'],
	    			'first_name'=>$_data['name_kh'],
	    			'last_name'=>$_data['name_en'],
	    			'sex'=>$_data['sex'],
	    			'dob'=>$_data['dob_client'],
	    			'pob'=>$_data['pob'],
	    			'nationality'=>$_data['nationality'],
	    			'document_type'=>$_data['client_d_type'],
	    			'doc_number'=>$_data['national_id'],
	    			'photo'=>$_data['photo'],
	    			'lang_note'=>$_data['desc'],
	    			'position_type'=>$_data['type'],
	    			'id_card'=>$_data['id_card'],
	    			'issue_date'=>$_data['issued_date'],
	    			'expired_date'=>$_data['expired_date'],
	    			'register_date'=>$_data['registered_date'],
	    			'experience_desc'=>$_data['experience'],
	    			'document_file'=>$_data['att_file'],
	    			'tel'=>$_data['phone'],
	    			'email'=>$_data['email'],
	    			'group_num'=>$_data['group'],
	    			'home_num'=>$_data['home'],
	    			'street'=>$_data['street'],
	    			'commune'=>$_data['commune'],
	    			'district'=>$_data['district'],
	    			'province_id'=>$_data['province'],
	    			'c_normalprice'=>$_data['cnormalprice'],
	    			'c_weekendprice'=>$_data['weekend_price'],
	    			'c_holidayprice'=>$_data['poblicholiday_price'],
	    			'c_otprice'=>$_data['cotprice'],
	    			'p_normalprice'=>$_data['pnormalprice'],
	    			'p_weekendprice'=>$_data['pweekend_price'],
	    			'p_holidayprice'=>$_data['ppoblicholiday_price'],
	    			'p_otprice'=>$_data['potprice'],
					'citynormalprice'=>$_data['citynormalprice'],
	    			'cityweekendprice'=>$_data['cityweekendprice'],
	    			'citypublicprice'=>$_data['citypublicprice'],
	    			'cityotprice'=>$_data['cityotprice'],
	    			'date'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    			'status'=>$_data['status'],
                                'monthly_price'	=>	$_data["monthly_price"],
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateDriver($_data){
    	
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    	$part= PUBLIC_PATH.'/images/driverphoto';
    	$adapter->setDestination($part);
        $adapter->receive();
    	
    	$photo = $adapter->getFileInfo();
    	if(!empty($photo['photo']['name'])){

    		$_data['photo']=$photo['photo']['name'];
    	}else{
    		$_data['photo']=$_data['old_photo'];
    	}
    	if(!empty($photo['att_file']['name'])){
    		$_data['att_file']=$photo['att_file']['name'];
    	}else{
    		$_data['att_file']=$_data['old_att_file'];
    	}
    	
    	$_arr = array(
    			'driver_id'=>$_data['client_no'],
    			'first_name'=>$_data['name_kh'],
    			'last_name'=>$_data['name_en'],
    			'sex'=>$_data['sex'],
    			'dob'=>$_data['dob_client'],
    			'pob'=>$_data['pob'],
    			'nationality'=>$_data['nationality'],
    			'document_type'=>$_data['client_d_type'],
    			'doc_number'=>$_data['national_id'],
    			'photo'=>$_data['photo'],
    			'lang_note'=>$_data['desc'],
    			'position_type'=>$_data['type'],
    			'id_card'=>$_data['id_card'],
    			'issue_date'=>$_data['issued_date'],
    			'expired_date'=>$_data['expired_date'],
    			'register_date'=>$_data['registered_date'],
    			'experience_desc'=>$_data['experience'],
    			'document_file'=>$_data['att_file'],
    			'tel'=>$_data['phone'],
    			'email'=>$_data['email'],
    			'group_num'=>$_data['group'],
    			'home_num'=>$_data['home'],
    			'street'=>$_data['street'],
    			'commune'=>$_data['commune'],
    			'district'=>$_data['district'],
    			'province_id'=>$_data['province'],
    			'c_normalprice'=>$_data['cnormalprice'],
    			'c_weekendprice'=>$_data['weekend_price'],
    			'c_holidayprice'=>$_data['poblicholiday_price'],
    			'c_otprice'=>$_data['cotprice'],
    			'p_normalprice'=>$_data['pnormalprice'],
    			'p_weekendprice'=>$_data['pweekend_price'],
    			'p_holidayprice'=>$_data['ppoblicholiday_price'],
    			'p_otprice'=>$_data['potprice'],
				'citynormalprice'=>$_data['citynormalprice'],
	    		'cityweekendprice'=>$_data['cityweekendprice'],
	    		'citypublicprice'=>$_data['citypublicprice'],
	    		'cityotprice'=>$_data['cityotprice'],
    			'user_id'=>$this->getUserId(),
    			'status'=>$_data['status'],
                        'monthly_price'	=>	$_data["monthly_price"],
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    public function getDriverById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name WHERE id = $id LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
 function getBranchById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT br_id,prefix,branch_namekh,branch_nameen,br_address,branch_code,branch_tel,fax,displayby,other,status FROM
    	$this->_name ";
    	$where = " WHERE `br_id`= $id LIMIT 1" ;
  
   		return $db->fetchRow($sql.$where);
    }
    public static function getBranchCode(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT COUNT(br_id) AS amount FROM `ln_branch`";
    	$acc_no= $db->getGlobalDbRow($sql);
    	$acc_no=$acc_no['amount'];
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	$pre = "";
    	for($i = $acc_no;$i<3;$i++){
    		$pre.='0';
    	}
    	return "C-".$pre.$new_acc_no;
    }
}  
	  
