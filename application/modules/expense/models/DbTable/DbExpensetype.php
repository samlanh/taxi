<?php

class Expense_Model_DbTable_DbExpensetype extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_expense_type';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    function getAllCustomerType($search = null){
    	$db = $this->getAdapter();
    	$where = " ";
    	$sql = " SELECT id,account_name,`status` FROM ln_expense_type   WHERE status=1 ";
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(name_en,' ','')  LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(name_kh,' ','')  LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status_search']>-1){
    		$where.= " AND status = ".$search['status_search'];
    	}
    
    	$order=" ORDER BY id DESC";
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
    
	public function addExpenseType($_data){
		try{
			$_arr=array(
				'account_name'	=> $_data['title'],
				'option_type'	=> $_data['c_type'],
				'date'			=> date("Y-m-d"),
				'status'  	    => $_data['status'],
				'user_id'  	    => $this->getUserId(),
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
	public function getCustomerTypeById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$db->quote($id)." AND type =10 ";
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	 
}

