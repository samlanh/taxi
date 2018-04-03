<?php

class Bookings_Model_DbTable_DbServiceType extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_booking_service_type';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    function getAllServiceType($search = null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title_en,note,DATE_FORMAT(create_date,'%d-%m-%Y') AS date,
			        (SELECT first_name FROM rms_users WHERE rms_users.id=user_id LIMIT 1) AS user_name,`status` 
			      FROM ldc_booking_service_type   
			      WHERE 1  ";
    	$where = " ";
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(title_en,' ','')  LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(note,' ','')  	LIKE '%{$s_search}%'";
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
    
	public function addSericeType($_data){
		try{
			$_arr=array(
				'title_en'		=> $_data['title'],
				'note'			=> $_data['remark'],
				'create_date'	=> date("Y-m-d  H:i:s"),
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
	
	function getServicetype($id){
		$db=$this->getAdapter();
		$sql=" SELECT * FROM ldc_booking_service_type WHERE id=$id";
		return $db->fetchRow($sql);
	}
	
	function getExpenstypeByOpt($opt){
		$db=$this->getAdapter();
		$sql="  SELECT id,account_name AS `name` FROM ln_expense_type WHERE option_type=$opt AND account_name!='' ";
		return $db->fetchAll($sql);
	}
	 
	function checkNumberBooking($num){
		$db=$this->getAdapter();
		$book_no = str_replace(' ', '', $num);
		$book_no=addslashes(trim($book_no));
		$sql="SELECT COUNT(b.payment_booking_no) AS number_book, b.payment_booking_no
		FROM ldc_carbooking AS b
		WHERE b.status=1 
		AND b.payment_booking_no!=''
		AND REPLACE(b.payment_booking_no,' ','')='$book_no'
		GROUP BY b.payment_booking_no";
		return $db->fetchRow($sql);
	}
	
	function checkCustomerBooking($num){
		$db=$this->getAdapter();
		$cus_name=addslashes(trim($num));
		$cus_name = str_replace(' ', '', $cus_name);
		$sql="SELECT COUNT(c.last_name) AS cus_name, b.payment_booking_no
		FROM ldc_carbooking AS b,ldc_customer AS c
		WHERE b.customer_id=c.id 
		AND b.status=1 
		AND c.last_name!=''
		AND REPLACE(c.last_name,' ','')='$cus_name'
		GROUP BY c.last_name";
		return $db->fetchRow($sql);
	}
}

