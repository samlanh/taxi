<?php

class Income_Model_DbTable_DbIncometype extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_income_type';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    function getAllIncomeType($search = null){
    	$db = $this->getAdapter();
    	$sql = " SELECT id,title,disc,`status` FROM ln_income_type  WHERE 1  ";
    	$where = " ";
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(title,' ','')  LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(disc,' ','')  LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status_search']>-1){
    		$where.= " AND status = ".$search['status_search'];
    	}
    	$order=" ORDER BY id DESC";
    	//print_r($sql.$where);
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function getlastKeycode($search = null){
    	$db = $this->getAdapter();
    	$where = " ";
    	$sql = " SELECT v.key_code FROM $this->_name AS v WHERE v.`type`=10 ";
    	$order=" ORDER BY id DESC LIMIT 1";
    	$row = $db->fetchOne($sql.$where.$order);
    	return $row+1;
    }
    
	public function addIncomeType($_data){
		try{
			$_arr=array(
				'title'	=> $_data['title'],
				'disc'	=> $_data['remark'],
				'date'	=> date("Y-m-d"),
				'status'=> $_data['status'],
				'user_id'=> $this->getUserId(),
			);
		
		if(!empty($_data['id'])){
			$where = 'id = '.$_data['id'];
			$this->update($_arr, $where);
			return 1;
		}else{
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function addCategoryType($_data){
		try{
			$_arr=array(
					'title'	=> $_data['title_cat'],
					'disc'	=> $_data['desc'],
					'date'	=> date("Y-m-d"),
					'status'=> $_data['status'],
					'user_id'=>$this->getUserId(),
			);
			return  $this->insert($_arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	
	public function addCustomerTypeAjax($_data){
		try{
			$keycode = $this->getlastKeycode();
			$_arr=array(
					'name_en'	  => $_data['custype_title'],
					'name_kh'	  => $_data['custype_title'],
					'key_code'	  => $keycode,
					'status'  => 1,
					'type'  => 10,
			);
			  $this->insert($_arr);
			  return $keycode;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	 
	
	function getIncometype($id){
		$db=$this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE id=$id";
		return $db->fetchRow($sql);
	}
	
	function getIncometypeByOpt(){
		$db=$this->getAdapter();
		$sql="  SELECT id,title AS `name` FROM ln_income_type WHERE `status`=1 AND title!='' ";
		return $db->fetchAll($sql);
	}
	 
}

