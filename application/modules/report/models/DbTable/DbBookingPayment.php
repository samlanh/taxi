<?php
class Report_Model_DbTable_DbBookingPayment extends Zend_Db_Table_Abstract
{
      function getAllVehiclePrice($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$sql="SELECT v.id,v.reffer,v.frame_no,v.licence_plate,
      	v.`year`,color,
      	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
      	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
      	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
      	(SELECT t.title FROM `ldc_vechicletye` AS t WHERE t.id=v.car_type) AS car_type,
         d.price,d.extraprice,d.vat_value,d.note,
         (SELECT day_title FROM `ldc_rankday` WHERE id=d.packageday_id LIMIT 1) As package_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =v.`status`) AS status
      	FROM ldc_vehicle AS v,ldc_vehiclefee_detail AS d   WHERE v.id=d.vehicle_id ";
      	$order = " GROUP BY v.id ORDER BY v.id DESC";
      	
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		 
      		$s_where[] = " v.reffer LIKE '%{$s_search}%'";
      		$s_where[] = " v.frame_no LIKE '%{$s_search}%'";
      		$s_where[] = " v.licence_plate LIKE '%{$s_search}%'";
      		$s_where[] = " v.`year` LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_make WHERE id=v.make_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_model WHERE id=v.model_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_submodel WHERE id=v.sub_model) LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['status']>-1){
      		$where .=' AND v.`status` = '.$search['status'];
      	}
      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllDriverPayment($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$sql="SELECT v.id,v.reffer,v.frame_no,v.licence_plate,
      	v.`year`,color,
      	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
      	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
      	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
      	(SELECT t.title FROM `ldc_vechicletye` AS t WHERE t.id=v.car_type) AS car_type,
      	d.price,d.extraprice,d.vat_value,d.note,
      	(SELECT day_title FROM `ldc_rankday` WHERE id=d.packageday_id LIMIT 1) As package_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =v.`status`) AS status
      	FROM ldc_vehicle AS v,ldc_vehiclefee_detail AS d   WHERE v.id=d.vehicle_id ";
      	$order = " GROUP BY v.id ORDER BY v.id DESC";
      	 
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		 
      		$s_where[] = " v.reffer LIKE '%{$s_search}%'";
      		$s_where[] = " v.frame_no LIKE '%{$s_search}%'";
      		$s_where[] = " v.licence_plate LIKE '%{$s_search}%'";
      		$s_where[] = " v.`year` LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_make WHERE id=v.make_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_model WHERE id=v.model_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_submodel WHERE id=v.sub_model) LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['status']>-1){
      		$where .=' AND v.`status` = '.$search['status'];
      	}
      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllVehicle($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$sql="SELECT v.id,v.reffer,v.frame_no,v.licence_plate,
      	v.`year`,color,
      	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
      	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
      	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
      	(SELECT t.title FROM `ldc_vechicletye` AS t WHERE t.id=v.car_type) AS car_type,
      	d.price,d.extraprice,d.vat_value,d.note,
      	(SELECT day_title FROM `ldc_rankday` WHERE id=d.packageday_id LIMIT 1) As package_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =v.`status`) AS status
      	FROM ldc_vehicle AS v,ldc_vehiclefee_detail AS d   WHERE v.id=d.vehicle_id ";
      	$order = " GROUP BY v.id ORDER BY v.id DESC";
      
//       	if (!empty($search['adv_search'])){
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['adv_search']));
      		 
//       		$s_where[] = " v.reffer LIKE '%{$s_search}%'";
//       		$s_where[] = " v.frame_no LIKE '%{$s_search}%'";
//       		$s_where[] = " v.licence_plate LIKE '%{$s_search}%'";
//       		$s_where[] = " v.`year` LIKE '%{$s_search}%'";
//       		$s_where[] = " (SELECT title FROM ldc_make WHERE id=v.make_id) LIKE '%{$s_search}%'";
//       		$s_where[] = " (SELECT title FROM ldc_model WHERE id=v.model_id) LIKE '%{$s_search}%'";
//       		$s_where[] = " (SELECT title FROM ldc_submodel WHERE id=v.sub_model) LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
//       	if ($search['status']>-1){
//       		$where .=' AND v.`status` = '.$search['status'];
//       	}
      	return $db->fetchAll($sql.$where.$order);
      }
      
      
      
 }

