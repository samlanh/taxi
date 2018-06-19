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
      	$from_date =(empty($search['start_date']))? '1': "m.create_date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': "m.create_date <= '".$search['end_date']." 23:59:59'";
      	$where = " WHERE ".$from_date." AND ".$to_date;
      	if(!empty($search['title'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['title']));
      		$s_search = str_replace(' ', '', $s_search);
      		$s_where[] = "REPLACE(m.invoice,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = "REPLACE(m.cheque_no,' ','')  	LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['status_search']>-1){
      		$where.= " AND m.status = ".$search['status_search'];
      	}
      	if($search['payment_method']>0){
      		$where.= " AND m.payment_type = ".$search['payment_method'];
      	}
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
     		 FROM ln_expense_maintenance AS em,ldc_vehicle AS v
		       WHERE em.vehicle_id=v.id
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
			$db = $this->getAdapter();
			$_db = new Application_Model_DbTable_DbGlobal();
			$lang = $_db->getCurrentLang();
			$array = array(1=>"name_en",2=>"name_kh");
			$sql=" SELECT d.id,d.`payment_no`,
					(SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_driverclear_payment_detail` AS pd WHERE pd.driverclear_id=d.id LIMIT 1)  ) AS booking_nos,
					(SELECT CONCAT(n.`last_name`,'(',n.`driver_id`,')') 
	 	           FROM `ldc_driver` AS n WHERE n.`status` =1 AND n.id=d.`driver_id` LIMIT 1) AS driver_name,
	 	            d.`payment_date`, (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=d.`payment_method` LIMIT 1) AS `payment_method`,
			       d.`total_driver_fee`,d.`total_driver_recived`,d.`paid_driver` ,
			      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=12 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
			      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name,d.`status`,
			      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=2 AND v.`status`=d.`status` LIMIT 1) AS `status`
			      FROM `ldc_driverclear_payment` AS d ";
			$where = '';
			$from_date =(empty($search['start_date']))? '1': "d.`payment_date` >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': " d.`payment_date` <= '".$search['end_date']." 23:59:59'";
			$where = " where ".$from_date." AND ".$to_date;
			if($search["search_text"] !=""){
				$s_search=addslashes(trim($search['search_text']));
				$s_search = str_replace(' ', '', $s_search);
				$s_where[]="REPLACE(d.`payment_no`,' ','')   LIKE '%{$s_search}%'";
				$s_where[]="REPLACE(d.`total_driver_fee`,' ','')   LIKE '%{$s_search}%'";
				$s_where[]="REPLACE(d.`total_driver_recived`,' ','')   LIKE '%{$s_search}%'";
				$s_where[]="REPLACE((SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_driverclear_payment_detail` AS pd WHERE pd.driverclear_id=d.id LIMIT 1)),' ','')   LIKE '%{$s_search}%'";
				$s_where[]="REPLACE(d.`paid_driver`,' ','')   LIKE '%{$s_search}%'";
				$where.=' AND ('.implode(' OR ',$s_where).')';
			}
			
			if ($search['driver_search']>0){
				$where.=" AND d.driver_id=".$search['driver_search'];
			}
			if ($search['status']>-1){
				$where.=" AND d.status=".$search['status'];
			}
			$order=' ORDER BY d.id DESC';
			return $db->fetchAll($sql.$where.$order);
		}
      
      function getAllDriverPymentById($id){
      	$db = $this->getAdapter();
			$_db = new Application_Model_DbTable_DbGlobal();
			$lang = $_db->getCurrentLang();
			$array = array(1=>"name_en",2=>"name_kh");
			$sql=" SELECT d.id,d.`payment_no`,d.`total_expense`,d.paid_driver,d.`balance`,
					(SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_driverclear_payment_detail` AS pd WHERE pd.driverclear_id=d.id LIMIT 1)  ) AS booking_nos,
					(SELECT CONCAT(n.`last_name`) 
	 	           FROM `ldc_driver` AS n WHERE n.`status` =1 AND n.id=d.`driver_id` LIMIT 1) AS driver_name,
	 	            d.`payment_date`, (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=d.`payment_method` LIMIT 1) AS `payment_method`,
			       d.`total_driver_fee`,d.`total_driver_recived`,d.`paid_driver` ,
			      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=12 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
			      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name,d.`status`,
			      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=2 AND v.`status`=d.`status` LIMIT 1) AS `status`,
			      (SELECT reffer FROM `ldc_vehicle` WHERE `ldc_vehicle`.id=(SELECT n.vehicle_id FROM `ldc_driver` AS n WHERE n.`status` =1 AND n.id=d.`driver_id` LIMIT 1)) AS car_no,
 (SELECT n.tel FROM `ldc_driver` AS n WHERE n.`status` =1 AND n.id=d.`driver_id` LIMIT 1) AS driver_phone
			      FROM `ldc_driverclear_payment` AS d  Where  d.id=$id  AND d.status=1";
      	return $db->fetchRow($sql);
      }
      
      function getAllCommission($search=null){
		$db = $this->getAdapter();
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql=" SELECT d.id,d.`payment_no`,
			   (SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_agencyclear_payment_detail` AS pd WHERE pd.clearagency_id=d.id LIMIT 1)  ) AS booking_nos,
		       (SELECT CONCAT(n.`last_name`,'(',n.`customer_code`,')') 
 	           FROM `ldc_agency` AS n WHERE n.`status` =1 AND n.id=d.`agency_id` LIMIT 1) AS agency_name,
 	           d.`payment_date`, (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=d.`payment_method` LIMIT 1) AS `payment_method`,
		       d.`total_commission`,d.`total_agen_recived`,d.`paid_agen` ,
		       (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=13 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
		      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name, 
		      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=2 AND v.`status`=d.`status` LIMIT 1) AS `status`
		      FROM `ldc_agencyclear_payment` AS d  ";
		$where =' ';
		$from_date =(empty($search['start_date']))? '1': "d.`payment_date` >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " d.`payment_date` <= '".$search['end_date']." 23:59:59'";
		$where = " where ".$from_date." AND ".$to_date;
		
		if($search["search_text"] !=""){
			$s_where=array();
			$s_search=addslashes(trim($search['search_text']));
			$s_search = str_replace(' ', '', $s_search);
			$s_where[]="REPLACE(d.`payment_no`,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(d.`total_commission`,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(d.`total_agen_recived`,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(d.`paid_agen`,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE((SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_agencyclear_payment_detail` AS pd WHERE pd.clearagency_id=d.id LIMIT 1)),' ','')   LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		
		if ($search['agency_search']>0){
			$where.=" AND d.`agency_id`=".$search['agency_search'];
		}
		if ($search['status']>0){
			$where.=" AND d.`status`=".$search['status'];
		}
		$order=' ORDER BY d.id DESC ';
		return $db->fetchAll($sql.$where.$order);
	}
      
      function getCustomerNearlyPayment($search){
	        $db = $this->getAdapter();
			$glob=new Application_Model_DbTable_DbGlobal();
			$lang= $glob->getCurrentLang();
			$array = array(1=>"name_en",2=>"name_kh");
			$where='';
			$sql=" SELECT cb.id,cb.`booking_no`,
					CONCAT(c.`last_name`,'(',customer_code,')') AS cus_name,
					l.`location_name` AS from_location,
					tl.`location_name` AS to_location,
					cb.`booking_date`,
					cb.`delivey_date`,cb.delivey_time,
					cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
					(SELECT CONCAT(d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
					(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
					cb.`status`
					FROM `ldc_carbooking` AS cb,
					`ldc_customer` AS c,
					`ldc_package_location` AS l,
					`ldc_package_location` AS tl
					WHERE 
					c.`id` = cb.`customer_id` 
					AND l.`id` = cb.`from_location`
					AND tl.`id` = cb.`to_location`
					AND cb.`status` >-1 AND cb.status_working=0 ";
			if($search["adv_search"] !=""){
				$s_where=array();
				$s_search=addslashes(trim($search['adv_search']));
				$s_where[]=" CONCAT(c.`last_name`,'(',customer_code,')') LIKE '%{$s_search}%'";
				$s_where[]=" cb.`booking_no` LIKE '%{$s_search}%'";
				$s_where[]=" tl.`location_name` LIKE '%{$s_search}%'";
				$s_where[]=" l.`location_name` LIKE '%{$s_search}%'";
				$s_where[]=" cb.`price` LIKE '%{$s_search}%'";
				$s_where[]=" cb.`commision_fee` LIKE '%{$s_search}%'";
				$s_where[]=" cb.`other_fee` LIKE '%{$s_search}%'";
				$s_where[]=" cb.`total` LIKE '%{$s_search}%'";
				$where.=' AND ('.implode(' OR ',$s_where).')';
			}
	      	if ($search['status']>-1){
	      		$where .=' AND cb.`status` = '.$search['status'];
	      	}
	      	if ($search['customer']>0){
	      		$where .=' AND  cb.`customer_id` = '.$search['customer'];
	      	}
			$str_next = '+1 1 days';
			$search['end_date']=date("Y-m-d", strtotime($search['end_date'].$str_next));
			$to_date = (empty($search['end_date']))? '1': " cb.`delivey_date` <= '".$search['end_date']." 23:59:59'";
			$where .= " AND ".$to_date;
			
			$order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
	      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getCommissionPaymentById($id){
      	$db = $this->getAdapter();
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql=" SELECT d.id,d.`payment_no`,d.balance,
			   (SELECT b.booking_no FROM `ldc_carbooking` AS b WHERE b.id=(SELECT pd.booking_id FROM `ldc_agencyclear_payment_detail` AS pd WHERE pd.clearagency_id=d.id LIMIT 1)  ) AS booking_nos,
		       (SELECT CONCAT(n.`last_name`) 
 	           FROM `ldc_agency` AS n WHERE n.`status` =1 AND n.id=d.`agency_id` LIMIT 1) AS agency_name,
 	           d.`payment_date`, (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=d.`payment_method` LIMIT 1) AS `payment_method`,
		       d.`total_commission`,d.`total_agen_recived`,d.`paid_agen` ,
		       (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=13 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
		      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name, 
		      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=2 AND v.`status`=d.`status` LIMIT 1) AS `status`,
		      (SELECT   n.phone FROM `ldc_agency` AS n WHERE n.`status` =1 AND n.id=d.`agency_id` LIMIT 1) AS agency_phone,
 	           (SELECT   n.email FROM `ldc_agency` AS n WHERE n.`status` =1 AND n.id=d.`agency_id` LIMIT 1) AS agency_email
		      FROM `ldc_agencyclear_payment` AS d where d.id=$id";
      	return $db->fetchRow($sql);
      }
      function getCommissionPaymentDetail($commission_payment_id){
      	$db = $this->getAdapter();
      	$sql="SELECT pd.*,
		(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_no,
		(SELECT c.payment_booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS payment_booking_no,
		(SELECT v.title FROM `ldc_vechicletye` AS v WHERE v.id=(SELECT c.vehicletype_id FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1)) AS vehicle_type,
		
		(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_date,
		
		(SELECT c.delivey_date FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS delivey_date ,
		(SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=
		(SELECT cb.from_location FROM `ldc_carbooking` AS cb WHERE cb.id =pd.`booking_id`) LIMIT 1 ) AS from_loc ,
                (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=
		(SELECT cb.to_location FROM `ldc_carbooking` AS cb WHERE cb.id =pd.`booking_id`) LIMIT 1 ) AS to_loc,
          (SELECT TIME_FORMAT(cb.`delivey_time`,'%H:%i') FROM `ldc_carbooking` AS cb WHERE cb.id= pd.booking_id LIMIT 1)  AS `time`
		FROM `ldc_agencyclear_payment_detail` AS pd ,`ldc_agencyclear_payment` AS a
		WHERE pd.`clearagency_id`=a.`id`
		AND pd.`clearagency_id`=$commission_payment_id";
      	return $db->fetchAll($sql);
      }
      
      function getDriverPaymentDetail($driver_payment_id){
      	$db = $this->getAdapter();
      	$sql="SELECT pd.*,
		(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_no,
		(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_date,
        (SELECT c.delivey_date FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS delivey_date ,
		(SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=
		(SELECT cb.from_location FROM `ldc_carbooking` AS cb WHERE cb.id =pd.`booking_id`) LIMIT 1 ) AS from_loc ,
                (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=
		(SELECT cb.to_location FROM `ldc_carbooking` AS cb WHERE cb.id =pd.`booking_id`) LIMIT 1 ) AS to_loc,

        (SELECT TIME_FORMAT(cb.`delivey_time`,'%H:%i') FROM `ldc_carbooking` AS cb WHERE cb.id= pd.booking_id LIMIT 1)  AS `time`,
		(SELECT c.`title` FROM `ldc_vechicletye` AS c WHERE c.id=(SELECT cb.`vehicletype_id` FROM `ldc_carbooking` AS cb WHERE cb.id= pd.booking_id)LIMIT 1)  AS `car_type` 
		
		FROM `ldc_driverclear_payment_detail` AS pd 
		WHERE pd.`driverclear_id`=$driver_payment_id";
      	return $db->fetchAll($sql);
      }
      
      function getAllCustomerPyment($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$_db = new Application_Model_DbTable_DbGlobal();
      	$lang = $_db->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$sql=" SELECT cb.id,cb.`payment_no`,
				CONCAT(c.`last_name`,'(',c.customer_code,')') AS customer,c.phone,cb.`payment_date`,
				(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cb.`payment_method` LIMIT 1) AS `payment_method`,
				cb.`grand_total`,cb.`paid`,cb.`balance`,
			    (SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`,
			    (SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name
			   FROM 
				`ldc_carbooking_payment` AS cb,
				`ldc_customer` AS c
			   WHERE 
				c.id = cb.`customer_id`
				AND cb.`status`>-1 ";
      	$order = "  ";
      	$from_date=(empty($search['start_date']))? '1': " cb.`payment_date` >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " cb.`payment_date` <= '".$search['end_date']." 23:59:59'";
      	$where = "  AND ".$from_date." AND ".$to_date;
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_search = str_replace(' ', '', $s_search);
      		$s_where[] = " REPLACE(CONCAT(c.`last_name`,'(',c.customer_code,')'),' ','') 	LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.phone,' ','') 			LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(cb.`payment_no`,' ','') 	LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(cb.`grand_total`,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(cb.`paid`,' ','') 		LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(cb.`balance`,' ','') 	LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['status']>-1){
      		$where .=' AND cb.`status` = '.$search['status'];
      	}
      	return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllCustomerPymentById($payment_id){
      	$db=$this->getAdapter();
      	$where =" ";
      	$_db = new Application_Model_DbTable_DbGlobal();
      	$lang = $_db->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$sql=" SELECT cb.id,cb.`payment_no`,c.phone,c.email,
      	CONCAT(c.`last_name`,'(',c.customer_code,')') AS customer,cb.`payment_date`,
      	(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cb.`payment_method` LIMIT 1) AS `payment_method`,
      	cb.`grand_total`,cb.`paid`,cb.`balance`,
      	(SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`,
      	(SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name
      	FROM
      	`ldc_carbooking_payment` AS cb,
      	`ldc_customer` AS c
      	WHERE
      	c.id = cb.`customer_id`
      	AND  cb.id=$payment_id";
      	$order = " LIMIT 1 ";
      	return $db->fetchRow($sql.$order);
      }
      
      function getCustomerPaymentDetail($customer_payment_id){
      	$db = $this->getAdapter();
      	$sql="SELECT pd.*,
      	(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_no,
      	(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.booking_id LIMIT 1) AS booking_date
      	FROM `ldc_carbooking_payment_detail` AS pd
      	WHERE pd.`payment_id`=$customer_payment_id";
      	return $db->fetchAll($sql);
      }
      
      function getAllCarBooking($search){
      	$db = $this->getAdapter();
      	$glob=new Application_Model_DbTable_DbGlobal();
      	$lang= $glob->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$from_date=$search["from_book_date"];
      	$to_date=$search["to_book_date"];
      	$sql=" SELECT cb.id,cb.*,cb.`booking_no`,c.last_name,c.phone,c.email,
      	(SELECT g.last_name FROM ldc_agency AS g WHERE g.id=cb.agency_id LIMIT 1) AS agency_name,
      	(SELECT v.title FROM ldc_vechicletye AS v WHERE v.id=cb.vehicletype_id LIMIT 1) AS vehicle_type,
      	l.`location_name` AS from_location,
      	tl.`location_name` AS to_location,
      	cb.`booking_date`,
      	cb.`delivey_date`,
      	cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
      	(SELECT CONCAT(d.`first_name`,' ',d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
      	(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
      	(SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`,
      	(SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name
      	FROM `ldc_carbooking` AS cb,
      	`ldc_package_location` AS l,
      	`ldc_package_location` AS tl,
      	ldc_customer AS c
      	WHERE
      	c.id=cb.customer_id
      	AND l.`id` = cb.`from_location`
      	AND tl.`id` = cb.`to_location`
      	AND cb.`status` >-1 ";
      	$where = '';
      
      	if($search['date_type']==2){
      	$from_date =(empty($search['from_book_date']))? '1': "cb.`delivey_date` >= '".$search['from_book_date']." 00:00:00'";
      	$to_date = (empty($search['to_book_date']))? '1': "cb.`delivey_date` <= '".$search['to_book_date']." 23:59:59'";
      	}
      	if($search['date_type']==1){
      	$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
      	$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
      	}
      	$where = "  AND ".$from_date." AND ".$to_date;
      	if($search["search_text"] !=""){
      	$s_where=array();
      	$s_search=addslashes(trim($search['search_text']));
      	$s_search = str_replace(' ', '', $s_search);
      	$s_where[]="REPLACE(c.last_name,' ','')   LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(c.phone,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(c.email,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`booking_no`,' ','')     LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(tl.`location_name`,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(l.`location_name`,' ','')   LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`price`,' ','')          LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`commision_fee`,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`other_fee`,' ','')      LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`total`,' ','')          LIKE '%{$s_search}%'";
      	$where.=' AND ('.implode(' OR ',$s_where).')';
      	}
      if ($search['agency_search']>0){
      		$where.=" AND cb.`agency_id`=".$search['agency_search'];
      }
      if ($search['vehicle_type']>0){
      $where.=" AND cb.`vehicletype_id`=".$search['vehicle_type'];
      }
      if ($search['working_status']>-1){
      $where.=" AND cb.`status_working`=".$search['working_status'];
      }
      if ($search['driver_search']>0){
      $where.=" AND cb.`driver_id`=".$search['driver_search'];
      }
      if ($search['agency_search']>0){
      	$where.=" AND cb.`agency_id`=".$search['agency_search'];
      }
      if ($search['customer']>0){
      $where.=" AND cb.`customer_id`=".$search['customer'];
      }
      if ($search['status']>-1){
      		$where.=" AND cb.`status`=".$search['status'];
      }
      $order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
      //echo $sql.$where.$order;
      return $db->fetchAll($sql.$where.$order);
      }
      
      function getCustomerAlertTime($search){
      	$db = $this->getAdapter();
      	$glob=new Application_Model_DbTable_DbGlobal();
      	$lang= $glob->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$where='';
      	$sql=" SELECT cb.id,cb.`booking_no`,
      	CONCAT(c.`last_name`,'(',customer_code,')') AS cus_name,
      	l.`location_name` AS from_location,
      	tl.`location_name` AS to_location,
      	cb.`booking_date`,
      	cb.`delivey_date`,
        cb.delivey_time,cb.delivey_time As time_zone,
      	cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
      	(SELECT CONCAT(d.`first_name`,' ',d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
      	(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
      	cb.`status`,(SELECT d.`tel` FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver_phone,c.phone As cus_phone,
      	(SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`
      	FROM `ldc_carbooking` AS cb,
      	`ldc_customer` AS c,
      	`ldc_package_location` AS l,
      	`ldc_package_location` AS tl
      	WHERE
      	c.`id` = cb.`customer_id`
      	AND l.`id` = cb.`from_location`
      	AND tl.`id` = cb.`to_location`
      	  ";
      	$from_date=(empty($search['from_book_date']))? '1': "cb.`delivey_date` >= '".$search['from_book_date']." 00:00:00'";
      	$to_date = (empty($search['to_book_date']))? '1': "cb.`delivey_date` <= '".$search['to_book_date']." 23:59:59'";
      	$where = "  AND ".$from_date." AND ".$to_date;
      	if($search["search_text"] !=""){
      	$s_where=array();
      	$s_search=addslashes(trim($search['search_text']));
      	$s_where[]=" CONCAT(c.`last_name`,'(',customer_code,')') LIKE '%{$s_search}%'";
      	$s_where[]=" cb.`booking_no` LIKE '%{$s_search}%'";
      	$s_where[]=" tl.`location_name` LIKE '%{$s_search}%'";
      	$s_where[]=" l.`location_name` LIKE '%{$s_search}%'";
      	$s_where[]=" cb.`price` LIKE '%{$s_search}%'";
      	$s_where[]=" cb.`commision_fee` LIKE '%{$s_search}%'";
      	$s_where[]=" cb.`other_fee` LIKE '%{$s_search}%'";
      	$s_where[]=" cb.`total` LIKE '%{$s_search}%'";
      	$where.=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['status']>-1){
      	$where .=' AND cb.`status` = '.$search['status'];
      }
      if ($search['customer']>0){
      $where .=' AND  cb.`customer_id` = '.$search['customer'];
      }
      if ($search['working_status']>-1){
      	$where.=" AND cb.`status_working`=".$search['working_status'];
      }
      if ($search['driver_search']>0){
			$where.=" AND cb.`driver_id`=".$search['driver_search'];
		}
      return $db->fetchAll($sql.$where);
      }
      
      function getAllIncomeDetail($search = null){
      	$db = $this->getAdapter();
      	$sql = "SELECT e.id,e.title,e.invoice,e.payment_type,e.total_amount,e.cheque_no,create_date,
      	(SELECT name_en FROM tb_view WHERE `key_code`=e.payment_type AND `type`=15 LIMIT 1) AS payment_type,
      	ed.income_type_id,ed.total_amount,ed.description,
      	(SELECT t.title FROM ln_income_type AS t WHERE t.id=ed.income_type_id LIMIT 1) AS income_type,
      	(SELECT first_name FROM rms_users WHERE rms_users.id=e.user_id LIMIT 1) AS user_name,
      	(SELECT name_en FROM tb_view WHERE tb_view.key_code=e.status AND tb_view.type=5 LIMIT 1) AS `status`
      	FROM ln_income AS e,ln_income_detail AS ed
      	WHERE e.id=ed.income_id ";
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
      		
      		$s_where[] = "REPLACE((SELECT t.title FROM ln_income_type AS t WHERE t.id=ed.income_type_id LIMIT 1),' ','') LIKE '%{$s_search}%'";
      		
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
      
      function getAllCarBookingPayment($search){
      	$db = $this->getAdapter();
      	$glob=new Application_Model_DbTable_DbGlobal();
      	$lang= $glob->getCurrentLang();
      	$array = array(1=>"name_en",2=>"name_kh");
      	$from_date=$search["from_book_date"];
      	$to_date=$search["to_book_date"];
      	$sql=" SELECT cb.id,cb.*,cb.`booking_no`,c.last_name,c.phone,c.email,
      	(SELECT g.last_name FROM ldc_agency AS g WHERE g.id=cb.agency_id LIMIT 1) AS agency_name,
      	(SELECT v.title FROM ldc_vechicletye AS v WHERE v.id=cb.vehicletype_id LIMIT 1) AS vehicle_type,
      	l.`location_name` AS from_location,
      	tl.`location_name` AS to_location,
      	cb.`booking_date`,
      	cb.`delivey_date`,
      	cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
      	(SELECT CONCAT(d.`first_name`,' ',d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
      	(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
      	(SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`,
      	(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.paid_status AND v.type=18 LIMIT 1) AS p_status,
        (SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.balance_status AND v.type=19 LIMIT 1) AS b_status,
      	(SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name
      	FROM `ldc_carbooking` AS cb,
      	`ldc_package_location` AS l,
      	`ldc_package_location` AS tl,
      	ldc_customer AS c
      	WHERE
      	c.id=cb.customer_id
      	AND l.`id` = cb.`from_location`
      	AND tl.`id` = cb.`to_location`
      	AND cb.`status` >-1 ";
      	$where = '';
      
      	if($search['date_type']==2){
      	$from_date =(empty($search['from_book_date']))? '1': "cb.`delivey_date` >= '".$search['from_book_date']." 00:00:00'";
      	$to_date = (empty($search['to_book_date']))? '1': "cb.`delivey_date` <= '".$search['to_book_date']." 23:59:59'";
      	}
      	if($search['date_type']==1){
      		$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
      		$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
      	}
      	$where = "  AND ".$from_date." AND ".$to_date;
      	if($search["search_text"] !=""){
      	$s_where=array();
      	$s_search=addslashes(trim($search['search_text']));
      	$s_search = str_replace(' ', '', $s_search);
      	$s_where[]="REPLACE(c.last_name,' ','')   LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(c.phone,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(c.email,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`booking_no`,' ','')     LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`payment_booking_no`,' ','')     LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(tl.`location_name`,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(l.`location_name`,' ','')   LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`price`,' ','')          LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`commision_fee`,' ','')  LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`other_fee`,' ','')      LIKE '%{$s_search}%'";
      	$s_where[]="REPLACE(cb.`total`,' ','')          LIKE '%{$s_search}%'";
      	$where.=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if ($search['agency_search']>0){
      	$where.=" AND cb.`agency_id`=".$search['agency_search'];
      	}
      	if ($search['vehicle_type']>0){
      	$where.=" AND cb.`vehicletype_id`=".$search['vehicle_type'];
      	}
      	if ($search['working_status']>-1){
      	$where.=" AND cb.`status_working`=".$search['working_status'];
      	}
      	if ($search['driver_search']>0){
      	$where.=" AND cb.`driver_id`=".$search['driver_search'];
      	}
      	if ($search['agency_search']>0){
      	$where.=" AND cb.`agency_id`=".$search['agency_search'];
      	}
       if ($search['customer']>0){
      				$where.=" AND cb.`customer_id`=".$search['customer'];
       }
      if ($search['status']>-1){
      $where.=" AND cb.`status`=".$search['status'];
      }
      $order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
      //echo $sql.$where.$order;
      return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllAgencyPaid($search=null){
          $db = $this->getAdapter();
          $glob=new Application_Model_DbTable_DbGlobal();
          $lang= $glob->getCurrentLang();
          $array = array(1=>"name_en",2=>"name_kh");
          
          $sql=" SELECT cb.id,cb.*,cb.`booking_no`,c.last_name,c.phone,c.email,REPLACE(c.last_name,' ','') As check_name,REPLACE(cb.payment_booking_no,' ','') As check_book_no,
          (SELECT g.last_name FROM ldc_agency AS g WHERE g.id=cb.agency_id LIMIT 1) AS agency_name,
          (SELECT v.title FROM ldc_vechicletye AS v WHERE v.id=cb.vehicletype_id LIMIT 1) AS vehicle_type,
          l.`location_name` AS from_location,
          tl.`location_name` AS to_location,
          cb.`booking_date`,
          cb.`delivey_date`,
          cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
          (SELECT CONCAT(d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
          (SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
          cb.`status`,
          (SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name,
          REPLACE(cb.payment_booking_no,' ','') AS numbooking,
          (SELECT SUM(sd.total_amount)  FROM ldc_booking_service_detial  AS sd WHERE sd.carbooking_id=cb.id) AS total_service,
          cb.`is_paid_commission`,cb.`is_paid_to_driver`=1,
          TIME_FORMAT(cb.`delivey_time`,'%H:%i')  AS `time`,
          (SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`
          FROM `ldc_carbooking` AS cb,
          `ldc_package_location` AS l,
          `ldc_package_location` AS tl,
          ldc_customer AS c
          WHERE
          c.id=cb.customer_id
          AND l.`id` = cb.`from_location`
          AND tl.`id` = cb.`to_location`
          ";
          $from_date =(empty($search['start_date']))? '1': " cb.`delivey_date` >= '".$search['start_date']." 00:00:00'";
          $to_date = (empty($search['end_date']))? '1': " cb.`delivey_date` <= '".$search['end_date']." 23:59:59'";
          $where = " AND ".$from_date." AND ".$to_date;
          
          	    if($search["search_text"] !=""){
          	        $s_where=array();
          	        $s_search=addslashes(trim($search['search_text']));
          	        $s_search = str_replace(' ', '', $s_search);
          	        $s_where[]="REPLACE(c.last_name,' ','')   LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(c.phone,' ','')  LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(c.email,' ','')  LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`booking_no`,' ','')     LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`payment_booking_no`,' ','')     LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(tl.`location_name`,' ','')  LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(l.`location_name`,' ','')   LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`price`,' ','')          LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`commision_fee`,' ','')  LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`other_fee`,' ','')      LIKE '%{$s_search}%'";
          	        $s_where[]="REPLACE(cb.`total`,' ','')          LIKE '%{$s_search}%'";
          	        $where.=' AND ('.implode(' OR ',$s_where).')';
          	    }
          // 	    // echo $sql.$where.$order;
          // 	    if ($search['agency_search']>0){
          // 	        $where.=" AND cb.`agency_id`=".$search['agency_search'];
          // 	    }
          // 	    if ($search['vehicle_type']>0){
          // 	        $where.=" AND cb.`vehicletype_id`=".$search['vehicle_type'];
          // 	    }
          // 	    if ($search['working_status']>-1){
          // 	        $where.=" AND cb.`status_working`=".$search['working_status'];
          // 	    }
          // 	    if ($search['driver_search']>0){
          // 	        $where.=" AND cb.`driver_id`=".$search['driver_search'];
          // 	    }
          	    if ($search['agency_search']>0){
          	        $where.=" AND cb.`agency_id`=".$search['agency_search'];
          	    }
          	    if ($search['is_paid']>-1){
          	        $where.=" AND cb.`is_paid_commission`=".$search['is_paid'];
          	    }
          	    if ($search['status']>-1){
          	        $where.=" AND cb.`status`=".$search['status'];
          	    }
         // AND cb.`is_paid_commission`=1
          $order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
          //echo $sql.$where;
          return $db->fetchAll($sql.$where.$order);
      }
      
      function getAllADriverPaid($search=null){
          $db = $this->getAdapter();
          $glob=new Application_Model_DbTable_DbGlobal();
          $lang= $glob->getCurrentLang();
          $array = array(1=>"name_en",2=>"name_kh");
          
          $sql=" SELECT cb.id,cb.*,cb.`booking_no`,c.last_name,c.phone,c.email,REPLACE(c.last_name,' ','') As check_name,REPLACE(cb.payment_booking_no,' ','') As check_book_no,
          (SELECT g.last_name FROM ldc_agency AS g WHERE g.id=cb.agency_id LIMIT 1) AS agency_name,
          (SELECT v.title FROM ldc_vechicletye AS v WHERE v.id=cb.vehicletype_id LIMIT 1) AS vehicle_type,
          l.`location_name` AS from_location,
          tl.`location_name` AS to_location,
          cb.`booking_date`,
          cb.`delivey_date`,
          cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
          (SELECT CONCAT(d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
          (SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
          cb.`status`,
          (SELECT first_name FROM rms_users WHERE rms_users.id=cb.user_id LIMIT 1) AS user_name,
          REPLACE(cb.payment_booking_no,' ','') AS numbooking,
          (SELECT SUM(sd.total_amount)  FROM ldc_booking_service_detial  AS sd WHERE sd.carbooking_id=cb.id) AS total_service,
          cb.`is_paid_commission`,cb.`is_paid_to_driver`=1,
          TIME_FORMAT(cb.`delivey_time`,'%H:%i')  AS `time`,
          (SELECT name_en FROM tb_view WHERE tb_view.key_code=cb.status AND tb_view.type=5 LIMIT 1) AS `status`
          FROM `ldc_carbooking` AS cb,
          `ldc_package_location` AS l,
          `ldc_package_location` AS tl,
          ldc_customer AS c
          WHERE
          c.id=cb.customer_id
          AND l.`id` = cb.`from_location`
          AND tl.`id` = cb.`to_location`
          ";
          $from_date =(empty($search['start_date']))? '1': " cb.`delivey_date` >= '".$search['start_date']." 00:00:00'";
          $to_date = (empty($search['end_date']))? '1': " cb.`delivey_date` <= '".$search['end_date']." 23:59:59'";
          $where = " AND ".$from_date." AND ".$to_date;
          
          if($search["search_text"] !=""){
              $s_where=array();
              $s_search=addslashes(trim($search['search_text']));
              $s_search = str_replace(' ', '', $s_search);
              $s_where[]="REPLACE(c.last_name,' ','')   LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(c.phone,' ','')  LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(c.email,' ','')  LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`booking_no`,' ','')     LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`payment_booking_no`,' ','')     LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(tl.`location_name`,' ','')  LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(l.`location_name`,' ','')   LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`price`,' ','')          LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`commision_fee`,' ','')  LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`other_fee`,' ','')      LIKE '%{$s_search}%'";
              $s_where[]="REPLACE(cb.`total`,' ','')          LIKE '%{$s_search}%'";
              $where.=' AND ('.implode(' OR ',$s_where).')';
          }
          // 	    // echo $sql.$where.$order;
          // 	    if ($search['agency_search']>0){
          // 	        $where.=" AND cb.`agency_id`=".$search['agency_search'];
          // 	    }
          // 	    if ($search['vehicle_type']>0){
          // 	        $where.=" AND cb.`vehicletype_id`=".$search['vehicle_type'];
          // 	    }
          // 	    if ($search['working_status']>-1){
          // 	        $where.=" AND cb.`status_working`=".$search['working_status'];
          // 	    }
          // 	    if ($search['driver_search']>0){
          // 	        $where.=" AND cb.`driver_id`=".$search['driver_search'];
          // 	    }
          if ($search['driver_search']>0){
              $where.=" AND cb.`driver_id`=".$search['driver_search'];
          }
          if ($search['is_paid']>-1){
              $where.=" AND cb.`is_paid_to_driver`=".$search['is_paid'];
          }
          if ($search['status']>-1){
              $where.=" AND cb.`status`=".$search['status'];
          }
          // AND cb.`is_paid_to_driver`=0
          $order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
          //echo $sql.$where;
          return $db->fetchAll($sql.$where.$order);
      }
      
 }

