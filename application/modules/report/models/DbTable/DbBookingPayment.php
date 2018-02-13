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
      
      function getAllExpenseMaintenance($search = null){
      	$db = $this->getAdapter();
      	$sql = " SELECT  m.id,m.invoice,m.cheque_no,
		      	(SELECT reffer FROM ldc_vehicle WHERE ldc_vehicle.id=m.vehicle_id LIMIT 1) AS car_no,
		      	(SELECT name_en FROM tb_view WHERE `key_code`=m.payment_type AND `type`=15 LIMIT 1) AS payment_type,title,
		      	SUM(total_amount) as total_amount,create_date,
		      	(SELECT first_name FROM rms_users WHERE rms_users.id=m.user_id LIMIT 1) AS user_name,`status`
		      	FROM ln_expense_maintenance AS m  ";
//       	$from_date =(empty($search['start_date']))? '1': "m.create_date >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': "m.create_date <= '".$search['end_date']." 23:59:59'";
//       	$where = " WHERE ".$from_date." AND ".$to_date;
      	$where=" where 1";
//       	if(!empty($search['title'])){
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['title']));
//       		$s_search = str_replace(' ', '', $s_search);
//       		$s_where[] = "REPLACE(m.invoice,' ','') LIKE '%{$s_search}%'";
//       		$s_where[] = "REPLACE(m.title,' ','') LIKE '%{$s_search}%'";
//       		$s_where[] = "REPLACE(m.cheque_no,' ','')  	LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
//       	if($search['status_search']>-1){
//       		$where.= " AND m.status = ".$search['status_search'];
//       	}
//       	if($search['payment_method']>0){
//       		$where.= " AND m.payment_type = ".$search['payment_method'];
//       	}
//       	if($search['vehicle_id']>0){
//       		$where.= " AND m.vehicle_id = ".$search['vehicle_id'];
//       	}
      	$order=" GROUP BY vehicle_id ORDER BY id DESC";
      	//echo $sql.$where;
      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getDriverInfor($id){
      	$db=$this->getAdapter();
      	$sql="SELECT em.id,em.vehicle_id,em.*,
		        d.driver_id,d.first_name,d.last_name,d.sex,d.photo,d.dob,d.nationality,d.tel,d.id_card,
		        (SELECT name_en FROM tb_view WHERE key_code=d.sex AND TYPE=13 LIMIT 1)AS gender
		        FROM ln_expense_maintenance AS em,ldc_vehicle AS v,ldc_driver AS d
		        WHERE em.vehicle_id=v.id
		        AND v.id=d.vehicle_id 
		        AND em.id=$id";
      	return $db->fetchRow($sql);
      }
      
      function getCarInfor($id){
      	$db=$this->getAdapter();
      	$sql="SELECT em.id,em.vehicle_id,em.*,
	         	 v.id,v.`reffer`,v.`year`,v.`color`,v.`seat_amount`,v.`img_front`,
			 	 (SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
				 (SELECT vt.title FROM ldc_vechicletye AS vt WHERE vt.id=v.car_type LIMIT 1) AS cat_type,
				 v.`frame_no`,v.`max_weight`,
				 v.`steering`,v.`test_url`,v.`show_url`,
				 v.`img_front_right`,v.img_seat,
				 v.`is_promotion`,v.`discount`,(SELECT m.title FROM `ldc_make` AS m WHERE m.id=v.`make_id`)
				 AS make,(SELECT md.title FROM `ldc_model` AS md WHERE md.id=v.`model_id`) AS model,
				 (SELECT sm.title FROM `ldc_submodel` AS sm WHERE sm.id=v.`sub_model`) AS sub_model,
				 (SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
				 (SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine`
     		 FROM ln_expense_maintenance AS em,ldc_vehicle AS v,ldc_driver AS d
		       WHERE em.vehicle_id=v.id
		       AND v.id=d.vehicle_id 
		       AND em.id=$id";
      	return $db->fetchRow($sql);
      }
      
      function getVehicleMaintenantDetail($id){
      	$db=$this->getAdapter();
      	$sql="SELECT  m.id,m.invoice,m.cheque_no,
                   (SELECT reffer FROM ldc_vehicle WHERE ldc_vehicle.id=m.vehicle_id LIMIT 1) AS car_no,
			       (SELECT name_en FROM tb_view WHERE `key_code`=m.payment_type AND `type`=15 LIMIT 1) AS payment_type,title,
			         CONCAT('$ ',m.total_amount) AS all_total,m.create_date,
			        (SELECT first_name FROM rms_users WHERE rms_users.id=m.user_id LIMIT 1) AS user_name,m.status,
			 (SELECT t.account_name FROM ln_expense_type AS t WHERE t.id=md.expense_type_id AND t.option_type=1 ) AS expen_type,
			 md.total_amount,md.description
		     FROM ln_expense_maintenance AS m,ln_expense_maintenance_detail AS md
		     WHERE m.id=md.expense_id
		     AND md.expense_id=$id";
      	return $db->fetchAll($sql);
      }
      
      function getAllExpenseDetail($search = null){
      	$db = $this->getAdapter();
      	$sql = "SELECT e.id,e.title,e.invoice,e.payment_type,e.total_amount,e.cheque_no,create_date,
					(SELECT name_en FROM tb_view WHERE `key_code`=e.payment_type AND `type`=15 LIMIT 1) AS payment_type,
					 ed.expense_type_id,ed.total_amount,ed.description,
					 (SELECT t.account_name FROM ln_expense_type AS t WHERE t.id=ed.expense_type_id AND t.option_type=2 LIMIT 1) AS expen_type,
				        (SELECT first_name FROM rms_users WHERE rms_users.id=e.user_id LIMIT 1) AS user_name,
				        (SELECT name_en FROM tb_view WHERE tb_view.key_code=e.status AND tb_view.type=5 LIMIT 1) AS `status`
				FROM ln_expense AS e,ln_expense_detail AS ed
				WHERE e.id=ed.expense_id ";
      	      	$from_date =(empty($search['start_date']))? '1': "e.create_date >= '".$search['start_date']." 00:00:00'";
      	      	$to_date = (empty($search['end_date']))? '1': "e.create_date <= '".$search['end_date']." 23:59:59'";
      	      	$where = " AND ".$from_date." AND ".$to_date;
      
      	      	if(!empty($search['title'])){
      	      		$s_where = array();
      	      		$s_search = addslashes(trim($search['title']));
      	      		$s_search = str_replace(' ', '', $s_search);
      	      		$s_where[] = "REPLACE(e.invoice,' ','') LIKE '%{$s_search}%'";
      	      		$s_where[] = "REPLACE(e.title,' ','') LIKE '%{$s_search}%'";
      	      		$s_where[] = "REPLACE(e.cheque_no,' ','')  	LIKE '%{$s_search}%'";
      	      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	      	}
      	      	if($search['status_search']>-1){
      	      		$where.= " AND e.status = ".$search['status_search'];
      	      	}
      	      	if($search['payment_method']>0){
      	      		$where.= " AND e.payment_type = ".$search['payment_method'];
      	      	}
      	$order=" ORDER BY id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllDriverPyment($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$_db = new Application_Model_DbTable_DbGlobal();
      	$lang = $_db->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$sql="SELECT 
				cp.`id`,cp.`payment_no`,
				CONCAT(a.`first_name`,' ',a.`last_name`) AS driver_name,
				cp.`payment_date`,
				(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cp.`payment_method` LIMIT 1) AS `payment_method`,
				(SELECT b.booking_no FROM ldc_carbooking AS b WHERE b.id=dpd.booking_id LIMIT 1) AS booking_no,
				 dpd.`due_amount`,dpd.`paid`,dpd.`remain`, 
				  (SELECT first_name FROM rms_users WHERE rms_users.id=cp.user_id LIMIT 1) AS user_name,
				  (SELECT name_en FROM tb_view WHERE tb_view.key_code=cp.status AND tb_view.type=5 LIMIT 1) AS `status`
				FROM `ldc_driver_payment` AS cp,ldc_driver_payment_detail AS dpd,
				`ldc_driver` AS a
				WHERE
				a.`id` = cp.`driver_id`
				AND cp.id=dpd.driver_payment_id";
      	$order = "  ";
      	 
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_search = str_replace(' ', '', $s_search);
      		
      		$s_where[] = " REPLACE(cp.`payment_no`,' ','') 	LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(dpd.`due_amount`,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(dpd.`paid`,' ','') 		LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(dpd.`remain`,' ','') 	LIKE '%{$s_search}%'";
      		$s_where[] = "(SELECT b.booking_no FROM ldc_carbooking AS b WHERE b.id=dpd.booking_id LIMIT 1) LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['status']>-1){
      		$where .=' AND cp.`status` = '.$search['status'];
      	}
      	return $db->fetchAll($sql.$where.$order);
      }
      
 }

