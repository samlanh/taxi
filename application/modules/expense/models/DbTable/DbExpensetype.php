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
    	$sql = "SELECT id,
			        (SELECT v.name_kh FROM tb_view AS v WHERE v.key_code=ln_expense_type.option_type AND TYPE=16 LIMIT 1)AS type_hevicel,
			         account_name,(SELECT first_name FROM rms_users WHERE rms_users.id=ln_expense_type.user_id LIMIT 1) AS user_name,
			        `status` 
			      FROM ln_expense_type   
			      WHERE 1 ";
    	$where = " ";
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(account_name,' ','')  LIKE '%{$s_search}%'";
    		 
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
//     	if($search['status_search']>-1){
//     		$where.= " AND status = ".$search['status_search'];
//     	}
    	if(!empty($search['c_type'])){
    		$where.= "  AND option_type = ".$search['c_type'];
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
	
	public function addCategoryType($_data,$type){
		try{
			$_arr=array(
					'account_name'	=> $_data['title_cat'],
					'option_type'	=> $type,
					'date'			=> date("Y-m-d"),
					'status'  	    => $_data['status'],
					'user_id'  	    => $this->getUserId(),
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
	public function getCustomerTypeById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$db->quote($id)." AND type =10 ";
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	
	function getExpenstype($id){
		$db=$this->getAdapter();
		$sql=" SELECT * FROM ln_expense_type WHERE id=$id";
		return $db->fetchRow($sql);
	}
	
	function getExpenstypeByOpt($opt){
		$db=$this->getAdapter();
		$sql="  SELECT id,account_name AS `name` FROM ln_expense_type WHERE option_type=$opt AND account_name!='' ";
		return $db->fetchAll($sql);
	}
	 
}

