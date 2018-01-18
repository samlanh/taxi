<?php
class Report_Model_DbTable_DbSale extends Zend_Db_Table_Abstract
{
	public function getAllSaleAgreement($search){
		$db = $this->getAdapter();
		$start_date = $search["start_date"];
		$end_date = $search["end_date"];
		$sql="SELECT 
				  a.id,
				  a.ag_code,
    			  v.`year`,
    			  (SELECT m.`title` FROM `ldc_make` AS m  WHERE m.`id` = v.`make_id`) AS make,
				  (SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id` = v.`model_id`) AS model,
				  (SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id` = v.`sub_model`) AS sub_model,
				  v.`licence_plate`,
				  CONCAT(o.`first_name`,' ',o.`last_name`) AS o_name,
				  o.`phone` AS o_phone,
    			  CONCAT(c.`first_name`,' ',c.`last_name`) AS name,
				  c.`phone`,
				  a.`total_price`,
				  a.`balance`,
				  a.date
				FROM
				  `ldc_vehicle` AS v,
				  ldc_customer AS c,
				  ldc_customer AS o,
				  `ldc_car_sale_aggreement` AS a 
				WHERE a.`vehicle_id` = v.id 
				  AND a.`owner_id` = o.`id` 
				  AND a.`customer_id` = c.`id`
				  AND a.`date` BETWEEN '$start_date' AND '$end_date'";
		$where ="";
		if($search["adv_search"]!=""){
			$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " a.ag_code LIKE '%{$s_search}%'";
      		$s_where[] = " v.licence_plate LIKE '%{$s_search}%'";
      		$s_where[] = " o_name LIKE '%{$s_search}%'";
      		$s_where[] = " name LIKE '%{$s_search}%'";
      		$s_where[] = " phone LIKE '%{$s_search}%'";
      		$s_where[] = " o_phone LIKE '%{$s_search}%'";
      		
      		$where .=' AND '.implode(' OR ',$s_where).'';
		}
		
		if(!empty($search["status"])){
			$where.=" AND a.`status` =".$search["status"];
		}
		$order= " ORDER BY id DESC";
// 		echo $sql.$where;
		return $db->fetchAll($sql.$where.$order);
	}
	
public function getAllSaleReciept($search){
		$db = $this->getAdapter();
		$start_date = $search["start_date"];
		$end_date = $search["end_date"];
		$sql="SELECT
				  cr.id,
    			 a.`ag_code`,
				  cr.`re_no`,
				  v.`year`,
				  (SELECT m.`title` FROM `ldc_make` AS m  WHERE m.`id` = v.`make_id`) AS make,
				  (SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id` = v.`model_id`) AS model,
				  (SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id` = v.`sub_model`) AS sub_model,
				  v.`licence_plate`,
				  CONCAT(o.`first_name`,' ',o.`last_name`) AS o_name,
				  o.`phone` AS o_phone,
				  CONCAT(c.`first_name`,' ',c.`last_name`) AS `name`,
				  c.`phone`,
				  cr.`total_price`,
				  cr.`amout_paid`,
				  cr.`balance`,
				  cr.`date`
				FROM
				  `ldc_vehicle` AS v,
				  ldc_customer AS c,
				  ldc_customer AS o,
				  `ldc_car_sale_aggreement` AS a ,
				  `ldc_car_sale_reciept` AS cr
				WHERE a.`vehicle_id` = v.id 
				  AND a.`owner_id` = o.`id` 
				  AND a.`customer_id` = c.`id` 
				  AND a.`id`=cr.`sale_id`
				  AND a.`date` BETWEEN '$start_date' AND '$end_date'";
		$where ="";
		if($search["adv_search"]!=""){
			$s_where = array();
      		$s_search = addslashes(trim($data['adv_search']));
      		$s_where[] = " a.ag_code LIKE '%{$s_search}%'";
      		$s_where[] = " cr.`re_no` LIKE '%{$s_search}%'";
      		$s_where[] = " v.licence_plate LIKE '%{$s_search}%'";
      		$s_where[] = " o_name LIKE '%{$s_search}%'";
      		$s_where[] = " name LIKE '%{$s_search}%'";
      		$s_where[] = " phone LIKE '%{$s_search}%'";
      		$s_where[] = " o_phone LIKE '%{$s_search}%'";
      		
      		$where .=' AND '.implode(' OR ',$s_where).'';
		}
		
		if($search["status"]!=""){
			$where.=" AND a.`status` =".$search["status"];
		}
		$order= " ORDER BY id DESC";
// 		echo $sql.$where;
		return $db->fetchAll($sql.$where.$order);
	}
}