<?php
class Report_Model_DbTable_DbRptAgreement extends Zend_Db_Table_Abstract
{
	protected $_name = 'ldc_vehicleagreement';
	
// 	function getAllVehicleAgreement($data){
// 		$db = $this->getAdapter();
// 		$sql ="SELECT id,agreement_code,(SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) AS owner_name,
// 		(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=$this->_name.customer_id LIMIT 1) AS customer_name,
// 		booking_id,
// 		(SELECT reffer FROM `ldc_vehicle` WHERE id=$this->_name.vehicle_id LIMIT 1) AS reffer,
// 		inception_date,return_date,return_time,period ,price_perday,date_create
// 		FROM $this->_name WHERE 1 ";
// 		$where='';
// 		if (!empty($data['adv_search'])){
// 			$s_where = array();
// 			$s_search = addslashes(trim($data['adv_search']));
			 
// 			$s_where[] = " agreement_code LIKE '%{$s_search}%'";
// 			$s_where[] = " (SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) LIKE '%{$s_search}%'";
// 			$s_where[] = " (SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=$this->_name.customer_id LIMIT 1) LIKE '%{$s_search}%'";
// 			$s_where[] = " (SELECT reffer FROM `ldc_vehicle` WHERE id=$this->_name.vehicle_id LIMIT 1) LIKE '%{$s_search}%'";
// 			$s_where[] = " inception_date LIKE '%{$s_search}%'";
// 			$where .=' AND ('.implode(' OR ',$s_where).')';
// 		}
// 		$order = ' ORDER BY id DESC ';
// 		return $db->fetchAll($sql.$where.$order);
// 	}
	function getAllVehicleAgreementNew($data){
		$db = $this->getAdapter();
		$from_date=$data["from_book_date"];
		$to_date=$data["to_book_date"];
		$sql ="
		SELECT agv.id,agv.agreement_code,
		(SELECT owner_name FROM `ldc_owner` WHERE id=agv.ownder_id LIMIT 1) AS owner_name, 
		(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=agv.customer_id LIMIT 1) AS customer_name, 
		agv.booking_id, 
		(SELECT b.booking_no FROM `ldc_booking` AS b WHERE b.id = agv.`booking_id` LIMIT 1) AS booking_no,
		agv.inception_date,agv.return_date,agv.return_time,agv.period ,
		agv.date_create FROM `ldc_agreementvehicle` AS agv WHERE 1";
		$where='';
		$where.=" AND agv.`date_create`>='$from_date' AND agv.`date_create`<='$to_date'";
		if (!empty($data['search_text'])){
			$s_where = array();
			$s_search = addslashes(trim($data['search_text']));
	
			$s_where[] = " agreement_code LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=$this->_name.customer_id LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " inception_date LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		$order = ' ORDER BY id DESC ';
		return $db->fetchAll($sql.$where.$order);
	}
// 	function getAllVehicleAgreementById($id){
// 		/*$db = $this->getAdapter();
// 		$sql ="SELECT a.id,a.agreement_code,a.agreement_date, a.booking_id,`a`.vehicle_id,
// 		a.sunday_price,a.airport_price,a.dropairport_price,a.item_1,a.item_2,a.item_3,a.witness,
// 		a.refundable,art1_id,toart1_id,art2_id,toart2_id,art3_id,toart3_id,regular_id,unlimited,repare,insurance,fule,fuel_full,
// 		a.inception_date,a.return_date,a.return_time,a.period ,a.price_perday,amount_price,refundable,longdist_acc,grand_total,discount_value,
// 		vat_amount,	a.date_create,c.*,(SELECT province_name FROM `ldc_province` WHERE id=c.province_id) AS province_name ,
// 		(SELECT country_name FROM `ldc_country` WHERE id=c.country) As country_name,
// 		w.*
// 		FROM `ldc_vehicleagreement` AS a,ldc_customer AS c,ldc_owner AS w WHERE
// 		c.id=a.customer_id AND a.ownder_id=w.id AND a.id= $id LIMIT 1";
// 		return $db->fetchRow($sql);*/
// 		$dbgb = new Application_Model_DbTable_DbGlobal();
// 		$lang= $dbgb->getCurrentLang();
// 		$array = array(1=>"province_en_name",2=>"province_kh_name");
// 		$db = $this->getAdapter();
// 		$sql ="SELECT a.id,a.is_familybook,a.is_idcard,a.is_passport,a.agreement_code,a.agreement_date, a.booking_id,`a`.vehicle_id,
// 		a.sunday_price,a.airport_price,a.dropairport_price,a.item_1,a.item_2,a.item_3,a.witness,
// 		a.refundable,art1_id,toart1_id,art2_id,toart2_id,art3_id,toart3_id,regular_id,unlimited,repare,insurance,fule,fuel_full,
// 		a.inception_date,a.return_date,a.return_time,a.period ,a.price_perday,amount_price,refundable,longdist_acc,grand_total,discount_value,
// 		vat_amount,	a.date_create,c.*,(SELECT ".$array[$lang]." FROM `ldc_province` WHERE id=c.province_id) AS province_name ,
// 		(SELECT country_name FROM `ldc_country` WHERE id=c.country) As country_name,
// 		w.*
// 		FROM `ldc_vehicleagreement` AS a,ldc_customer AS c,ldc_owner AS w WHERE
// 		c.id=a.customer_id AND a.ownder_id=w.id AND a.id= $id LIMIT 1";
// 		return $db->fetchRow($sql);
// 	}

	function getAllVehicleAgreementByIdNew($id){
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$db = $this->getAdapter();
		$sql ="SELECT 
		 a.*,
		c.*,
		(SELECT ".$array[$lang]." FROM `ldc_province` WHERE id=c.province_id) AS province_name ,
			 (SELECT country_name FROM `ldc_country` WHERE id=c.country) AS country_name, w.* 
		FROM `ldc_agreementvehicle` AS a,ldc_customer AS c,ldc_owner AS w 
		WHERE c.id=a.customer_id AND a.ownder_id=w.id AND a.id= $id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getVehicleAgreementDetail($agreement_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_agreementvehicle_detail` AS agd WHERE agd.`agreement_id` = $agreement_id";
		return $db->fetchAll($sql);
	}
	public function getVehicleById($id){
		$db = $this->getAdapter();
		$sql = "SELECT v.reffer,v.frame_no,v.licence_plate,v.color,
		(SELECT title FROM ldc_make WHERE id=v.make_id) AS make,
		(SELECT title FROM ldc_model WHERE id=v.model_id) AS model,
		(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
		v.year,v.seat_amount,v.engine_number,chassis_no,
		(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
		(SELECT type FROM `ldc_type` WHERE id = v.type AND STATUS=1) AS type,
		v.licence_plate
		FROM `ldc_vehicle` AS v WHERE v.status=1 AND v.reffer!='' AND v.id = $id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	////driver / tour guide
	function getAllDriverAgreement($data){
		$this->_name='ldc_driveragreement';
		$db = $this->getAdapter();
		$sql ="SELECT id,agreement_code,(SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) AS owner_name,
		(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=$this->_name.customer_id LIMIT 1) AS customer_name,
		booking_id,
		start_date,finish_date,return_time,period ,date_create
		FROM $this->_name where 1 order by id DESC ";
		return $db->fetchAll($sql);
	}
	function getDriverAgreementById($id){
		$db = $this->getAdapter();
		$sql ="SELECT a.id,a.agreement_code,a.agreement_date, a.booking_id,a.rental_fee,a.witness,
		a.refundable,art1_id,toart1_id,a.is_passport,a.is_idcard,a.is_familybook,
		a.start_date,a.finish_date as return_date,a.return_time,a.period ,refundable,grand_total,
		a.date_create,c.*,(SELECT province_name FROM `ldc_province` WHERE id=c.province_id) AS province_name ,
		(SELECT country_name FROM `ldc_country` WHERE id=c.country) As country_name,
		w.*
		FROM `ldc_driveragreement` AS a,ldc_customer AS c,ldc_owner AS w WHERE
		c.id=a.customer_id AND a.ownder_id=w.id AND a.id= $id LIMIT 1";
		return $db->fetchRow($sql);

	}
	function getDriverByBooking($booking_id){
		$db = $this->getAdapter();
		$sql="SELECT dd.*,d.* FROM `ldc_booking_detail` as dd ,ldc_driver as d WHERE d.id=dd.item_id AND dd.book_id=$booking_id AND dd.item_type=2";
		return $db->fetchAll($sql);
	}
/********product agreement*******/
	function getAllProductAgreement($data){
		$this->_name='ldc_driveragreement';
		$db = $this->getAdapter();
		$sql ="SELECT id,agreement_code,(SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) AS owner_name,
		(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=ldc_productagreement.customer_id LIMIT 1) AS customer_name,
		booking_id,
		start_date,finish_date,return_time,period ,date_create
		FROM ldc_productagreement where 1 order by id DESC ";
// 		echo $sql;exit();
		return $db->fetchAll($sql);
	}
	function getProductAgreementById($id){
		$db = $this->getAdapter();
		$sql ="SELECT a.id,a.agreement_code,a.agreement_date, a.booking_id,a.rental_fee,a.witness,
		a.refundable,art1_id,toart1_id,
		a.start_date,a.finish_date as return_date,a.return_time,a.period ,refundable,grand_total,
		a.date_create,c.*,(SELECT province_name FROM `ldc_province` WHERE id=c.province_id) AS province_name ,
		(SELECT country_name FROM `ldc_country` WHERE id=c.country) As country_name,
		w.*
		FROM `ldc_productagreement` AS a,ldc_customer AS c,ldc_owner AS w WHERE
		c.id=a.customer_id AND a.ownder_id=w.id AND a.id= $id LIMIT 1";
// 		echo $sql;exit();
		return $db->fetchRow($sql);
	}
	function getProductBooking($booking_id){
		$db = $this->getAdapter();
		$sql="SELECT b.*,p.* FROM `ldc_booking_detail` As b,ldc_stuff as p  WHERE b.book_id=$booking_id AND b.item_type=3 AND p.id=b.item_id ";
		return $db->fetchAll($sql);
	}
}