<?php

class Location_Model_DbTable_DbProvince extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_province';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    	 
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	if(!empty($session_lang->lang_id)){
    		return $session_lang->lang_id;
    	}else{
    		return 2;
    	}
    }
    public function addNewProvince($_data){
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$photoname = str_replace(" ", "_", $_data['kh_province']) . '.jpg';
		$upload = new Zend_File_Transfer();
		$upload->addFilter('Rename',
				array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
		$receive = $upload->receive();
		if($receive)
		{
			$_data['photo'] = $photoname;
		}
		else{
			$_data['photo']="";
		}
    	$_arr=array(
    			'province_en_name'=>$_data['en_province'],
    		    'province_kh_name' => $_data['kh_province'],
    			'photo' =>$_data['photo'],
    			'note' => $_data['note'],
    			'modify_date'      => date("Y-d-m H:i"),
    			'create_date'      => date("Y-d-m H:i"),
    			'status'           => $_data['status'],
    			'user_id'	       => $this->getUserId()
    	);
    	return  $this->insert($_arr);
    }
    
	public function getProvinceById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$id;
		$sql.=" LIMIT 1";
		$row=$db->fetchRow($sql);
		return $row;
	}
    public function updateProvince($_data,$id){
    	$photoname = str_replace(" ", "_", $_data['kh_province']) . '.jpg';
    	$upload = new Zend_File_Transfer();
    	$upload->addFilter('Rename',
    			array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
    	$receive = $upload->receive();
    	if($receive)
    	{
    		$_data['photo'] = $photoname;
    	}
    	else{
    		$_data['photo']=$_data['old_photo'];
    	}
    	$_arr=array(
    			'province_en_name'=>$_data['en_province'],
    		    'province_kh_name' => $_data['kh_province'],
    			'photo' =>$_data['photo'],
    			'note' => $_data['note'],
    			'modify_date'      => date("Y-d-m H:i"),
    			'status'           => $_data['status'],
    			'user_id'	       => $this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $id);
    	$this->update($_arr, $where);
    }
    function getAllProvince($search=null){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$sql = " SELECT id,".$array[$lang]." as province_name,create_date,status,
    	(SELECT first_name FROM rms_users WHERE id=user_id )AS user_name
    	FROM $this->_name
    	WHERE province_en_name!='' ";
    	$order=" order by id DESC";
    	$where = '';
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['title']);
    		$s_where[]=" province_en_name LIKE '%{$s_search}%'";
    		$s_where[]=" province_kh_name LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if($search['status']>-1){
    		$where.= " AND status = ".$db->quote($search['status']);
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
    function getAllLocationType(){
    	$db = $this->getAdapter();
    	$sql = " SELECT id ,title FROM `ldc_locationtype` WHERE status=1 AND title!=''  ";
    	$order=" order by id DESC";
    	return $db->fetchAll($sql.$order);
    	}
   
}

