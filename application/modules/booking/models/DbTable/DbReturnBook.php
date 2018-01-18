<?php

class Booking_Model_DbTable_DbReturnBook extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_booking';
    
    public function getAllBooking($search){
    	$db = $this->getAdapter();
    	$booking_date = $search["date_book"];
    	$sql="SELECT b.id,b.`customer_id`,(SELECT CONCAT(c.`first_name`,' ',c.`last_name`) FROM `ldc_customer` AS c WHERE c.`id`=b.`customer_id`) AS customer_name,b.`booking_no`,b.`pickup_date`,b.`date_book`,b.`return_date`,b.`total_fee`,b.`deposite_fee` FROM `ldc_booking` AS b WHERE b.`status`=1 OR b.`status`=2";
    	$where="";
    	if($search["adv_search"]!=""){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" booking_no LIKE '%{$s_search}%'";
    		$s_where[]=" customer_name LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if($search["book_no"]!=-1){
    		$where.= " AND b.`id`=".$search['book_no'];
    	}
    	return $db->fetchAll($sql.$where);
    }
    public function getBookingById($id,$type){
    	$db = $this->getAdapter();
    	if($type==1){
    		$sql="SELECT b.*,c.`first_name`,c.`last_name`,c.`sex`,c.`phone`,c.`email` FROM `ldc_booking` AS b,`ldc_customer` AS c WHERE b.`id`=$id AND b.`customer_id`=c.`id`";
    		return $db->fetchRow($sql);
    	}else{
	    	$sql="SELECT b.* FROM `ldc_booking_detail` AS b WHERE b.`book_id`=$id";
	    	return $db->fetchAll($sql);
    	}
    }
    public function getBookingNo(){
    	$db = $this->getAdapter();
    	$sql ="SELECT b.id,b.`booking_no` FROM `ldc_booking` AS b WHERE b.`status`=1 OR b.`status`=2";
    	return $db->fetchAll($sql);
    }
    
    public function ReturnBook($id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$row = $this->getBookingById($id, 1);
    	$row_detail = $this->getBookingById($id, 2);
    	try{
    	$arr = array(
    		'book_id'			=>	$row["id"],
    		'customer_id'		=>	$row["customer_id"],
			'booking_no'		=>	$row["booking_no"],
			'pickup_location'	=> $row["pickup_location"],
			'dropoff_location'	=>	$row["dropoff_location"],
			'date_book'			=>	$row["date_book"],
			'pickup_date'		=>	$row["pickup_date"],
			'pickup_time'		=>	$row["pickup_time"],
			'return_date'		=>	$row["return_date"],
			'return_time'		=>	$row["return_time"],
			'total_fee'			=>	$row["total_fee"],
			'deposite_fee'		=>	$row["deposite_fee"],
			'total_vat'			=>	$row["total_vat"],
			'total_paymented'	=>	$row["total_paymented"],
			'status'			=>	$row["status"],
			'visa_name'			=>	$row["visa_name"],
			'card_id'			=>	$row["card_id"],
			'secu_code'			=>	$row["secu_code"],
			'card_exp_date'		=>	$row["card_exp_date"],
			'payment_type'		=>	$row["payment_type"],
			'status'			=>	$row["status"],
			'item_type'			=>	$row["item_type"],
			'fly_no'				=> 	$row["fly_no"],
			'fly_date'				=>	$row["fly_date"],
			'fly_time_of_arrival'	=>	$row["fly_time_of_arrival"],
			'fly_destination'		=>	$row["fly_destination"]
    	);
    	$this->_name="ldc_return_booking";
    	$return_id = $this->insert($arr);
    	
    	foreach ($row_detail as $rs){
    		$arr_booking_detail = array(
    				'return_id'			=>	$return_id,
    				'book_id'			=>	$rs["book_id"],
    				'item_id'			=> 	$rs["item_id"],
    				'item_name'			=> 	$rs["item_name"],
    				'rent_num'			=>	$rs["rent_num"],
    				'price'				=>	$rs["price"],
    				'VAT'				=>	$rs["VAT"],
    				'discount'			=>	$rs["discount"],
    				'deposite'			=>	$rs["deposite"],
    				'refund_deposit'	=>	$rs["refund_deposit"],
    				'total'				=>	$rs["total"],
    				'total_paymented'	=>	$rs["deposite"],
    				'item_type'			=>	$rs["item_type"],
    				'status'			=>	$rs["status"],
    		);
    		$this->_name = "ldc_return_booking_detail";
    		$this->insert($arr_booking_detail);
    	}
    	$update = array(
    		'status'	=>	3,
    	);
    	$this->_name="ldc_booking";
    	$where = $db->quoteInto("id=?", $id);
    	$this->update($update, $where);
    	$db->commit();
    	}catch (Exception $e){
    		$db->rollBack();
    		echo $e->getMessage();
                Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }
}  
	  

