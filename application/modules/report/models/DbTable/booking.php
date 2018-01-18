<?php
class Report_Model_DbTable_booking extends Zend_Db_Table_Abstract
{
	public function getAllBooking($search){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$date_book = $search["date_book"];
		$pickup_date = $search["pickup_date"];
		$return_date = $search["return_date"];
		$sql='SELECT b.`id`,b.`booking_no`,
		(SELECT customer_code FROM `ldc_customer` AS c WHERE c.`id`=b.`customer_id` LIMIT 1) AS customercode,
		(SELECT CONCAT(c.`first_name`," ",c.`last_name`) FROM `ldc_customer` AS c WHERE c.`id`=b.`customer_id` LIMIT 1) AS customer,
		(SELECT p.'.$array[$lang].' FROM `ldc_province` AS p WHERE p.`id`=b.`pickup_location` LIMIT 1) AS pickup_lc,
		(SELECT p.'.$array[$lang].' FROM `ldc_province` AS p WHERE p.`id`=b.`dropoff_location` LIMIT 1) AS return_lc,
		b.`date_book`,CONCAT(b.`pickup_date`," ",b.`pickup_time`) AS pickup_date,CONCAT(b.`return_date`," ",b.`return_time`) AS return_date,
		b.`total_vat`,b.`total_fee`,b.`deposite_fee`,b.`total_paymented`,b.`payment_type` ';
		$sql.=",(SELECT SUM(bd.refund_deposit) FROM `ldc_booking_detail` AS bd WHERE bd.book_id = b.`id` ) AS total_refund_deposit";//new additional 27/11/2017
		$sql.=" FROM `ldc_booking` AS b WHERE 1";
		$where ="";
		if($search["customer"]!=-1){
			$where.=" AND b.`customer_id` =".$search["customer"];
		}
		
		if($search["book_no"]!=-1){
			$where.=" AND b.`booking_no` =".$search["book_no"];
		}
		
		if($search["status"]!=-1){
			$where.=" AND b.`status` =".$search["status"];
		}
	
		$order= " ORDER BY id DESC";
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function getBookNo(){
		$db = $this->getAdapter();
		$sql='SELECT b.`booking_no` FROM `ldc_booking` AS b ';
		return $db->fetchAll($sql);
	}
	
	public function getCustomerNo(){
		$db = $this->getAdapter();
		$sql='SELECT c.id, CONCAT(c.`first_name`," " ,c.`last_name`,"-",c.`customer_code`) AS customer FROM `ldc_customer` AS c WHERE status=1 ' ;
		return $db->fetchAll($sql);
	}
	function getBookingdetailAction(){
		$id=$this->getRequest()->getParam('id');
	}
}