<?php

class Bookings_Model_DbTable_DbService extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_booking_service';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    function getAllService($search = null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,service_title,
				 (SELECT title_en FROM ldc_booking_service_type WHERE ldc_booking_service_type.id= service_id LIMIT 1) AS service_id,
				 note,DATE_FORMAT(DATE,'%d-%m-%Y') AS DATE,
			     (SELECT first_name FROM rms_users WHERE rms_users.id=user_id LIMIT 1) AS user_name,`status` 
			    FROM ldc_booking_service WHERE 1 ";
    	$where = " ";
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(service_title,' ','')  LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(note,' ','')  	LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status_search']>-1){
    		$where.= " AND status = ".$search['status_search'];
    	}
    	if(!empty($search['service_type'])){
    		$where.= " AND service_id = ".$search['service_type'];
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
    
	public function addSerice($_data){
		try{
			$_arr=array(
				'service_title'		=> $_data['title'],
				'service_id'		=> $_data['service_type'],
				'description'		=> $_data['remark'],
				'date'				=> date("Y-m-d"),
				'status'  	    	=> $_data['status'],
				'user_id'  	    	=> $this->getUserId(),
			);
		if(!empty($_data['id'])){
			$where = ' id = '.$_data['id'];
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
	
	
	public function addServiceTypeAjax($_data){
		try{
			$_arr=array(
					'title_en'	 	=> $_data['title_s_t'],
					'note'	  		=> $_data['descript'],
					'create_date'	=> date("Y-m-d"),
					'status'  => 1,
			);
		$this->_name='ldc_booking_service_type';
		return	$this->insert($_arr);
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
	
	function getServiceById($id){
		$db=$this->getAdapter();
		$sql=" SELECT * FROM ldc_booking_service WHERE id=$id";
		return $db->fetchRow($sql);
	}
	
	function getExpenstypeByOpt($opt){
		$db=$this->getAdapter();
		$sql="  SELECT id,account_name AS `name` FROM ln_expense_type WHERE option_type=$opt AND account_name!='' ";
		return $db->fetchAll($sql);
	}
	
	function getSerictTypeOpt(){
		$db=$this->getAdapter();
		$sql="  SELECT id,title_en AS `name` FROM ldc_booking_service_type WHERE `status`=1 AND title_en!='' ";
		return $db->fetchAll($sql);
	}
	 
}

