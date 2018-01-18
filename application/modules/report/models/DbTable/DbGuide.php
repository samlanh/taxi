<?php
class Report_Model_DbTable_DbGuide extends Zend_Db_Table_Abstract
{
      public function getGuideInfo($data){//rpt-loan-released/
      	$db = $this->getAdapter();
      	$dbgb = new Application_Model_DbTable_DbGlobal();
      	$lang= $dbgb->getCurrentLang();
      	$array = array(1=>"province_en_name",2=>"province_kh_name");
      	$sql = "SELECT id,driver_id,first_name,last_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=1 AND key_code =ldc_driver.`sex`) AS sex ,tel,dob,pob,nationality,
      	(SELECT name_en FROM `ldc_view` WHERE type=`ldc_driver`.document_type LIMIT 1) AS doc_name,
      	doc_number,lang_note,position_type As type,
      	group_num,home_num,street,commune,district,
      	(SELECT ".$array[$lang]." FROM `ldc_province` WHERE `ldc_province`.id=province_id LIMIT 1) AS province_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =ldc_driver.`status`) AS status
      	FROM ldc_driver ";
      	$where = ' WHERE 1 ';
      	if(!empty($data['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($data['adv_search']));
      		$s_where[] = " driver_id LIKE '%{$s_search}%'";
      		$s_where[] = " first_name LIKE '%{$s_search}%'";
      		$s_where[] = " last_name LIKE '%{$s_search}%'";
      		$s_where[] = " tel LIKE '%{$s_search}%'";
      		$s_where[] = " pob LIKE '%{$s_search}%'";
      		$s_where[] = " nationality LIKE '%{$s_search}%'";
      		$s_where[] = " doc_number LIKE '%{$s_search}%'";
      		$s_where[] = " lang_note LIKE '%{$s_search}%'";
      		
      		$s_where[] = " group_num LIKE '%{$s_search}%'";
      		$s_where[] = " home_num LIKE '%{$s_search}%'";
      		$s_where[] = " street LIKE '%{$s_search}%'";
      		$s_where[] = " commune LIKE '%{$s_search}%'";
      		$s_where[] = " district LIKE '%{$s_search}%'";
      		
      		$where .=' AND '.implode(' OR ',$s_where).'';
      	}
      	$order=' ORDER BY id DESC';
//       	echo sql.$where.$order;
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getGuidePrice($search){//rpt-loan-released/
      	$db = $this->getAdapter();
      	$sql = "SELECT id,driver_id,first_name,last_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=1 AND key_code =ldc_driver.`sex`) AS sex ,tel,
      	c_normalprice,c_weekendprice,c_holidayprice,c_otprice,
      	citynormalprice,cityweekendprice,citypublicprice,cityotprice,
      	p_normalprice,p_weekendprice,p_holidayprice,p_otprice,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =ldc_driver.`status`) AS status
      	FROM ldc_driver ";
      	$where = ' WHERE 1 AND status=1 ';
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		
      		$s_where[] = " first_name LIKE '%{$s_search}%'";
      		$s_where[] = " last_name LIKE '%{$s_search}%'";
      		$s_where[] = " sex LIKE '%{$s_search}%'";
      		$s_where[] = " tel LIKE '%{$s_search}%'";
      		
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order=' ORDER BY id DESC';
      	return $db->fetchAll($sql.$where.$order);
      }
      function getAllVehicleInfo($search){
      	$db=$this->getAdapter();
      	$where =" ";
      	$sql="SELECT v.id,v.reffer,v.frame_no,v.licence_plate,v.`year`,max_weight,seat_amount,org_cost,
      	chassis_no,engine,engine_number,
      	(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,steering,color,
      	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
      	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
      	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
      	(SELECT `type` FROM ldc_type AS t WHERE t.id=v.type) AS `type`,
      	(SELECT t.title FROM `ldc_vechicletye` AS t WHERE t.id=v.car_type) AS car_type,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =v.`status`) AS status
      	FROM ldc_vehicle AS v  WHERE  1 ";
      	if ($search['status']>-1){
      		$where .=' AND v.`status` = '.$search['status'];
      	}
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      	
      		$s_where[] = " reffer LIKE '%{$s_search}%'";
      		$s_where[] = " frame_no LIKE '%{$s_search}%'";
      		$s_where[] = " licence_plate LIKE '%{$s_search}%'";
      		$s_where[] = " year LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_make WHERE id=v.make_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_model WHERE id=v.model_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT title FROM ldc_submodel WHERE id=v.sub_model) LIKE '%{$s_search}%'";
      		
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
    
      	$order = " GROUP BY v.id ORDER BY v.id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
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
      function getAllVehicleTaxiTour($search){
      	$sql="SELECT vehicle_id As id,(SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) AS vehicle_id,
      	(SELECT frame_no FROM ldc_vehicle WHERE id=frame_id) AS frame_id,
      	(SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,
      	(SELECT location_name FROM ldc_package_location WHERE id=package_id ) AS package_id,
      	price,one_hour,max_hour,note,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =status) AS status
      	FROM ldc_vehicletaxitour WHERE `status` = 1 ";
      	$where='';
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " (SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT frame_no FROM ldc_vehicle WHERE id=frame_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT location_name FROM ldc_package_location WHERE id=package_id ) LIKE '%{$s_search}%'";
      		$s_where[] = " price LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order=' ORDER BY id DESC';
      	return $this->getAdapter()->fetchAll($sql.$where.$order);
      }
      function getAllServicePrice(){
      	$sql="SELECT id,service_title,description,photo,price,`status`FROM ldc_serviceprice WHERE 1";
      	$db=$this->getAdapter();
      	$order=" ORDER BY  id DESC";
      	return $db->fetchAll($sql.$order);
      }
      function getAllCarprice($search){
      	$sql="SELECT vehicle_id As id,(SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) AS vehicle_id,
      	(SELECT frame_no FROM ldc_vehicle WHERE id=ldc_pickupcarprice.frame_no) As frame_no,
      	(SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,
      	(SELECT location_name FROM ldc_package_location WHERE id=form_location ) AS form_location,
      	(SELECT location_name FROM ldc_package_location WHERE id=to_location ) AS to_location,price,note,`status`
      	FROM ldc_pickupcarprice WHERE `status`=1";
      	$order=' ORDER BY id DESC';
      	$where='';
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		 
      		$s_where[] = " (SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT frame_no FROM ldc_vehicle WHERE id=ldc_pickupcarprice.frame_no) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT location_name FROM ldc_package_location WHERE id=form_location ) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT location_name FROM ldc_package_location WHERE id=to_location ) LIKE '%{$s_search}%'";
      		$s_where[] = " price LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	return $this->getAdapter()->fetchAll($sql.$where.$order);
      }
      function getAllVehicleTaxi(){
      	$sql="SELECT 
	      	v.id,v.reffer,v.frame_no,v.licence_plate,
	      	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
	      	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
	      	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
	      	(SELECT location_name FROM ldc_package_location WHERE id=t.from_location ) AS from_location,
	      	(SELECT location_name FROM ldc_package_location WHERE id=t.to_location ) AS to_location,distance,rate,
	      	price,discount_trip,round_trip,(SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,t.`status`
	      	FROM ldc_vehicle AS v,ldc_vehicletaxi AS t WHERE v.id=t.vehicle_id AND t.`status`=1 AND from_location!=0 AND to_location!=0 ";
      	$order=' ORDER BY id DESC';
      	return $this->getAdapter()->fetchAll($sql.$order);
      }
      function getAllVehicle(){
      	$db=$this->getAdapter();
      	$where ="WHERE 1";
      	$sql="SELECT id,reffer,`year`,
      	(SELECT title FROM ldc_make WHERE id=make_id) AS make_id,
      	(SELECT title FROM ldc_model WHERE id=model_id) AS model_id,
      	(SELECT title FROM ldc_submodel WHERE id=sub_model) AS sub_model,
      	(SELECT `type` FROM ldc_type AS t WHERE t.id=v.type) AS `type`,
      	color,(SELECT capacity FROM ldc_engince WHERE id=`engine`) AS `engine`,chassis_no,frame_no,licence_plate,date_buy,`status`
      	FROM ldc_vehicle As v ";
      
      	$order = " ORDER BY id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      function getAllClients(){
      	$db = $this->getAdapter();
      	$dbgb = new Application_Model_DbTable_DbGlobal();
      	$lang= $dbgb->getCurrentLang();
      	$array = array(1=>"province_en_name",2=>"province_kh_name");
      	$where = " WHERE (first_name!='' OR  last_name!='') ";
      	$sql = " SELECT id,customer_code,first_name,last_name,
      	(SELECT name_en FROM `ldc_view` WHERE TYPE=1 AND key_code =ldc_customer.`sex`) AS sex
      	,dob,phone,pob,nationality,company_name,
      	group_num,house_num,commune,district,
      	(SELECT ".$array[$lang]." FROM `ldc_province` WHERE `ldc_province`.id=province_id LIMIT 1) AS province_name
      	FROM ldc_customer ";
      	$order=" ORDER BY id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      function getCustomerInfoByBooking($booking_id){
      	$sql=" SELECT c.customer_code,c.first_name,c.last_name,c.sex,c.phone,c.email,
      			b.booking_no,
      			(SELECT location_name FROM `ldc_package_location` WHERE id=b.pickup_location LIMIT 1) AS pickup_location,
      			(SELECT location_name FROM `ldc_package_location` WHERE id=b.dropoff_location LIMIT 1) AS dropoff_location,
      			b.date_book,b.pickup_date,b.pickup_time,b.return_date,
      			b.return_time,b.total_fee,b.deposite_fee,b.total_vat,b.total_paymented,b.fly_no,b.fly_destination,b.`payment_type`,b.`card_id`,b.`visa_name`,b.`card_exp_date`,b.`secu_code`
      			FROM `ldc_customer` AS c,ldc_booking AS b 
      			WHERE c.id = b.customer_id AND b.id=$booking_id LIMIT 1";
      	$db= $this->getAdapter();
      	return $db->fetchRow($sql);
      }
      function getItemBookingDetail($booking_id){
      	$sql=" SELECT item_name,discount ,price,VAT,total,rent_num,refund_deposit FROM `ldc_booking_detail` WHERE book_id=$booking_id ";
      	$db= $this->getAdapter();
      	return $db->fetchAll($sql);
      }
      
      function getAgreementByBookingId($booking_id){ // 27-11-2017 for show on invoice detail footer owner name
      	$db = $this->getAdapter();
      	$sql="SELECT agv.*,
			owe.`owner_name`
			 FROM `ldc_agreementvehicle` AS agv,
			 `ldc_owner` AS owe
			  WHERE owe.`id` = agv.`ownder_id` AND agv.`booking_id` =$booking_id";
      	return $db->fetchRow($sql);
      	
      }
      
 }

