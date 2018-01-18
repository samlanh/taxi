<?php

class agreement_Model_DbTable_Carsale extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehicleagreement';
    
    public function getSystemSetting($keycode){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM `ln_system_setting` WHERE keycode ='".$keycode."' LIMIT 1";
    	return $db->fetchRow($sql);
    }
   
    public function getNewAgreementCode($date=null){
    	$db = $this->getAdapter();
    	$row = $this->getSystemSetting('saleagreement');
    	$sql=" SELECT COUNT(id) FROM `ldc_car_sale_aggreement` LIMIT 1 ";
    	$number = $db->fetchOne($sql);
    	$new_no= (int)$number+101;
    	$number= strlen((int)$number+1);
    	$sub='';
    	for($i = $number;$i<3;$i++){
    		$sub.='0';
    	}
    	if($date==null){
    		$sub=date("y")."-".date("m")."-".date("d")."-".$sub.$new_no;
    	}else{
    		$sub=date("y",strtotime($date))."-".date("m",strtotime($date))."-".date("d",strtotime($date))."-".$sub.$new_no;
    	}
    	$pre = ($row['value']);
    	return $pre."-".$sub;
    }
    public function getNewRecieptCode($date=null){
    	$db = $this->getAdapter();
    	$row = $this->getSystemSetting('carsalereciept');
    	$sql=" SELECT COUNT(id) FROM `ldc_car_sale_reciept` LIMIT 1 ";
    	$number = $db->fetchOne($sql);
    	$new_no= (int)$number+101;
    	$number= strlen((int)$number+1);
    	$sub='';
    	for($i = $number;$i<3;$i++){
    		$sub.='0';
    	}
    	if($date==null){
    		$sub=date("y")."-".date("m")."-".date("d")."-".$sub.$new_no;
    	}else{
    		$sub=date("y",strtotime($date))."-".date("m",strtotime($date))."-".date("d",strtotime($date))."-".$sub.$new_no;
    	}
    	$pre = ($row['value']);
    	return $pre."-".$sub;
    }
    
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    function getIdNamecustomer(){
    	$sql="SELECT id,customer_code,first_name,last_name FROM ldc_customer WHERE 1";
    	return $this->getAdapter()->fetchAll($sql);
    }
    function getVehiclerefNo(){
    	$sql="SELECT id,reffer FROM ldc_vehicle WHERE 1";
    	$order=' ORDER BY id DESC';
    	return $this->getAdapter()->fetchAll($sql.$order);
    }
    
    function getCustomerById($id){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$sql = "SELECT c.id,c.`first_name`,c.`last_name`,c.`sex`,c.company_name,c.`occupation`,c.`address1`,c.`address2`,c.`passport_name`,c.`nationality`,c.`name_oncard`,c.`phone`,c.`email`,
    	c.dob,c.group_num,c.house_num,c.street,c.commune,c.district,
    	(SELECT ".$array[$lang]." as `province_name` FROM ldc_province AS p WHERE p.`id`=c.`province_id`) AS province
    	 FROM ldc_customer AS c WHERE c.`id`=$id";
    	$row = $db->fetchRow($sql);
    	if($row){
    		return $row;
    	}
    }
    function getVehicleById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT v.`reffer`,
    	v.`frame_no`,v.`licence_plate`,
    	(SELECT m.`title` FROM `ldc_make` AS m WHERE m.`id`=v.`make_id`) AS make,
    	(SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id`=v.`model_id`) AS model,
    	(SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id`=v.`sub_model`) AS sub_model,
    	v.`year`,v.`chassis_no`,v.`engine_number`,v.`of_axlex`,v.`of_cylinder`,v.`color`,v.`cylinders_dip`,
    	(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.`id`=v.`engine`) AS hors_power,
    	(SELECT t.`type` FROM `ldc_type` AS t WHERE t.`id`=v.`car_type`) AS car_type,v.sale_price,
    	v.`licence_plate` FROM `ldc_vehicle` AS v  WHERE v.id=$id";
    	$row = $db->fetchRow($sql);
    	if($row){
    		return $row;
    	}
    }
    function getSaleAgreementById($id){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$sql ="SELECT 
				  a.id,
				  a.ag_code,
				  a.`sale_price`,
				  a.`discount`,
				  a.`tax`,
				  a.`total_price`,
				  a.`import_tax`,
				  a.`annual_road_tax`,
				  a.`vehicle_sertificate`,
				  a.`vehicle_id_card`,
				  a.`buyer_witness`,
				  a.`seller_witness`,
				  a.operation_type,
				  o.id AS o_id,
				  o.`first_name` AS o_f_name,
				  o.`last_name` AS o_l_name,
				  o.`sex` AS o_sex,
				  o.`dob` as o_dob,
				  o.`occupation` AS o_occupation,
				  o.`group_num` AS o_group_num,
				  o.`house_num` AS o_home_num,
				  o.`street` AS o_street,
				  o.`commune` AS o_commune,
				  o.`district` AS o_district,
				  (SELECT ".$array[$lang]." as `province_name` FROM ldc_province AS p WHERE p.`id`=o.`province_id`) AS o_province,
				  o.company_name AS o_company_name,
				  o.`address1` AS o_addr1,
				  o.`address2` AS o_addr2,
				  o.`passport_name` AS o_passport,
				  o.`nationality` AS o_national,
				  o.`name_oncard` AS o_nameoncard,
				  o.`phone` AS o_phone,
				  o.`email` AS o_email,
				  c.`first_name`,
				  c.`last_name`,
				  c.`sex`,
				  c.company_name,
				  c.`occupation`,
				  c.`group_num`,
				  c.`house_num`,
				  c.`street`,
				  c.`commune`,
				  c.`district`,
				  c.`dob` as c_dob,
				  (SELECT ".$array[$lang]." as `province_name` FROM `ldc_province` AS p WHERE p.`id`=c.`province_id`) AS provice,
				  c.`address1`,
				  c.`address2`,
				  c.`passport_name`,
				  c.`nationality`,
				  c.`name_oncard`,
				  c.`phone`,
				  c.`email`,
				  c.`id` AS c_id,
				  v.`reffer`,
				  v.`id` AS v_id,
				  v.`frame_no`,
				  v.`licence_plate`,
				  (SELECT m.`title` FROM `ldc_make` AS m  WHERE m.`id` = v.`make_id`) AS make,
				  (SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id` = v.`model_id`) AS model,
				  (SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id` = v.`sub_model`) AS sub_model,
				  v.`year`,
				  v.`chassis_no`,
				  v.`engine_number`,
				  v.`of_axlex`,
				  v.`of_cylinder`,
				  v.`color`,
				  v.`cylinders_dip`,
				  (SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.`id` = v.`engine`) AS hors_power,
				  (SELECT t.`type` FROM `ldc_type` AS t WHERE t.`id` = v.`car_type`) AS car_type,
				  v.`licence_plate`,
				  e.`capacity`
				FROM
				  `ldc_vehicle` AS v,
				  ldc_customer AS c,
				  ldc_customer AS o,
				  `ldc_engince` AS e,
				  `ldc_car_sale_aggreement` AS a 
				WHERE a.`vehicle_id` = v.id 
				  AND a.`owner_id` = o.`id` 
				  AND a.`customer_id` = c.`id`
				  AND v.`engine`=e.`id` 
				  AND a.`id` =$id";
    	$row = $db->fetchRow($sql);
    	if($row){
    		return $row;
    	}
    }
    
    function add($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$a_code = $this->getNewAgreementCode();
    	try{
	    	$arr = array(
	    		'vehicle_id' 			=>	$data["reffer"],
	    		'owner_id'				=>	$data["ven_name"],
	    		'customer_id'			=>	$data["cus_name"],
	    		'ag_code'				=>	$a_code,
	    		'invoice_no'			=>	$a_code,
	    		'sale_price'			=>	$data["sale_price"],
	    		'discount'				=>	$data["discount"],
	    		'tax'					=>	$data["tax"],
	    		'total_price'			=>	$data["total"],
	    		'balance'				=>	$data["total"],
	    		'import_tax'			=>	$data["tax_paid"],
	    		'annual_road_tax'		=>	$data["road_tax"],
	    		'vehicle_sertificate'	=>	$data["certificate_vehicle"],
	    		'vehicle_id_card'		=>	$data["id_card"],
	    		'buyer_witness'			=>	$data["buyer_witness"],
	    		'seller_witness'		=>	$data["seller_witness"],
	    		'date'					=>	date("Y-m-d"),
	    		'status'				=>	1,
	    		'user_id'				=>	$this->getUserId(),
	    		'operation_type'		=>	$data["opt_type"],
	    	);
	    	
	    	$this->_name = "ldc_car_sale_aggreement";
	    	$id = $this->insert($arr);
	    	
	    	$arr_u = array(
	    		'status'	=>	0,
	    	);
	    	$this->_name ="ldc_vehicle";
	    	$where = $this->getAdapter()->quoteInto("id=?", $data["reffer"]);
	    	$this->update($arr_u, $where);
	    	$db->commit();
	    	return $id;
    	
    	}catch (Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    		echo $e->getMessage();
    	}
    }
    
    function edit($data,$id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$a_code = $this->getNewAgreementCode();
    	try{
    		$arr = array(
    				'vehicle_id' 			=>	$data["reffer"],
    				'owner_id'				=>	$data["ven_name"],
    				'customer_id'			=>	$data["cus_name"],
    				//'ag_code'				=>	$a_code,
    				//'invoice_no'			=>	$a_code,
    				'sale_price'			=>	$data["sale_price"],
    				'discount'				=>	$data["discount"],
    				'tax'					=>	$data["tax"],
    				'total_price'			=>	$data["total"],
    				'balance'				=>	$data["total"],
    				'import_tax'			=>	$data["tax_paid"],
    				'annual_road_tax'		=>	$data["road_tax"],
    				'vehicle_sertificate'	=>	$data["certificate_vehicle"],
    				'vehicle_id_card'		=>	$data["id_card"],
    				'buyer_witness'			=>	$data["buyer_witness"],
    				'seller_witness'		=>	$data["seller_witness"],
    				'date'					=>	date("Y-m-d"),
    				'status'				=>	1,
    				'user_id'				=>	$this->getUserId(),
    				'operation_type'		=>	$data["opt_type"],
    		);
    
    		$this->_name = "ldc_car_sale_aggreement";
    		$wheres = $db->quoteInto("id=?", $id);
    		$id = $this->update($arr, $wheres);
    		
    		$arr_u = array(
    				'status'	=>	1,
    		);
    		$this->_name ="ldc_vehicle";
    		$where1 = $this->getAdapter()->quoteInto("id=?", $data["old_vehicle"]);
    		$this->update($arr_u, $where1);
    
    		$arr_u = array(
    				'status'	=>	0,
    		);
    		$this->_name ="ldc_vehicle";
    		$where = $this->getAdapter()->quoteInto("id=?", $data["reffer"]);
    		$this->update($arr_u, $where);
    		$db->commit();
    		return $id;
    		 
    	}catch (Exception $e){
    		$db->rollBack();
    		echo $e->getMessage();
    	}
    }
    
    // Reciept Payment
    
    function getInvoiceNo($type=null){
    	$db = $this->getAdapter();
    	$sql="SELECT c.`id`,c.`ag_code` FROM `ldc_car_sale_aggreement` AS c WHERE c.`status`=1 ";
    	$where ='';
    	if($type==1){
    		$where = " AND c.`is_completed`=0";
    	}
    	$row = $db->fetchAll($sql.$where);
    	if($row){
    		return $row;
    	}
    }
    function getInvoiceById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT 
				  a.id,
				  a.ag_code,
				  a.`total_price`,
				  a.`balance`,
				  o.id AS o_id,
				  o.`first_name` AS o_f_name,
				  o.`last_name` AS o_l_name,
				  o.`sex` AS o_sex,
				  o.`dob` AS o_dob,
				  o.`passport_name` AS o_passport,
				  o.`pass_issuedate` AS o_pass_issuedate,
				  o.`pass_expireddate` AS o_pass_expireddate,
				  o.`card_name` AS o_card_name,
				  o.`card_issuedate` AS o_card_issuedate,
				  o.`card_expireddate` AS o_card_expireddate,
				  o.`nationality` AS o_national,
				  o.`name_oncard` AS o_nameoncard,
				  o.`phone` AS o_phone,
				  o.`email` AS o_email,
				  c.`first_name`,
				  c.`last_name`,
				  c.`sex`,
				  c.`dob`,
				  c.`passport_name`,
				  c.`pass_issuedate`,
				  c.`pass_expireddate`,
				  c.`card_name`,
				  c.`card_issuedate`,
				  c.`card_expireddate`,
				  c.`nationality`,
				  c.`name_oncard`,
				  c.`phone`,
				  c.`email`,
				  v.`reffer`,
				  v.`frame_no`,
				  v.`licence_plate`,
				  (SELECT m.`title` FROM `ldc_make` AS m  WHERE m.`id` = v.`make_id`) AS make,
				  (SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id` = v.`model_id`) AS model,
				  (SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id` = v.`sub_model`) AS sub_model,
				  v.`year`,
				  v.`chassis_no`,
				  v.`engine_number`,
				  v.`of_axlex`,
				  v.`of_cylinder`,
				  v.`color`,
				  v.`cylinders_dip`,
				  (SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.`id` = v.`engine`) AS hors_power,
				  (SELECT t.`type` FROM `ldc_type` AS t WHERE t.`id` = v.`car_type`) AS car_type,
				  v.`licence_plate` 
				FROM
				  `ldc_vehicle` AS v,
				  ldc_customer AS c,
				  ldc_customer AS o,
				  `ldc_car_sale_aggreement` AS a 
				WHERE a.`vehicle_id` = v.id 
				  AND a.`owner_id` = o.`id` 
				  AND a.`customer_id` = c.`id` 
				  AND a.`id` =$id";
    	$row = $db->fetchRow($sql);
    	if($row){
    		return $row;
    	}
    }
    
    function addReceipt($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try {
    		if($data["paid_amount"] >= $data["total"]){
    			$is_complete = 1;
    		}else{
    			$is_complete = 0;
    		}
    		$arr = array(
    			'sale_id' 			=> $data["refer"],
    			're_no' 			=> $this->getNewRecieptCode(),
    			'total_price' 		=> $data["total"],
    			'pay_type'			=>	$data["pay_type"],
    			'amout_paid' 		=> $data["paid_amount"],
    			'balance' 			=> $data["balance"],
    			'date_full_pay' 	=> $data["date_will_paid"],
    			'time_full_pay' 	=> $data["time_paid"],
    			'date' 				=> date("Y-m-d"),
    			'status' 			=> 1,
    			'is_completed' 		=> $is_complete,
    			//'parent_id' 		=> $data[""],
    		);
    		$this->_name = "ldc_car_sale_reciept";
    		$id = $this->insert($arr);
    		
    		$arr_up = array(
    			'balance'			=> $data["balance"],
    			'parent_id' 		=> $id,
    			'is_completed' 		=> $is_complete,
    		);
    		$this->_name = "ldc_car_sale_aggreement";
    		$where = $db->quoteInto("id=?", $data["refer"]);
    		$this->update($arr_up, $where);
    		$db->commit();
    		return $id;
    	}catch (Exception $e){
    		$db->rollBack();
    		echo $e->getMessage();exit();
    	}
    }
    function editReceipt($data,$ids){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try {
    		if($data["paid_amount"] >= $data["total"]){
    			$is_complete = 1;
    		}else{
    			$is_complete = 0;
    		}
    		$arr = array(
    				'sale_id' 			=> $data["refer"],
    				're_no' 			=> $this->getNewRecieptCode(),
    				'total_price' 		=> $data["total"],
    				'pay_type'			=>	$data["pay_type"],
    				'amout_paid' 		=> $data["paid_amount"],
    				'balance' 			=> $data["balance"],
    				'date_full_pay' 	=> $data["date_will_paid"],
    				'time_full_pay' 	=> $data["time_paid"],
    				'date' 				=> date("Y-m-d"),
    				'status' 			=> 1,
    				'is_completed' 		=> $is_complete,
    				//'parent_id' 		=> $data[""],
    		);
    		$this->_name = "ldc_car_sale_reciept";
    		$where = $db->quoteInto("id=?", $ids);
    		$id = $this->update($arr, $where);
    
    		$arr_up = array(
    				'balance'			=> $data["old_price"],
    				//'parent_id' 		=> $id,
    				'is_completed' 		=> 0,
    		);
    		$this->_name = "ldc_car_sale_aggreement";
    		$where = $db->quoteInto("id=?", $data["old_sale_id"]);
    		$this->update($arr_up, $where);
    		
    		$arr_up = array(
    				'balance'			=> $data["balance"],
    				'parent_id' 		=> $id,
    				'is_completed' 		=> $is_complete,
    		);
    		$this->_name = "ldc_car_sale_aggreement";
    		$where = $db->quoteInto("id=?", $data["refer"]);
    		$this->update($arr_up, $where);
    		$db->commit();
    		return $id;
    	}catch (Exception $e){
    		$db->rollBack();
    		echo $e->getMessage();exit();
    	}
    }
    
    function getRecieptById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT
		    	cr.id,
		    	  cr.`sale_id`,
				  cr.`re_no`,
				  cr.`total_price`,
				  cr.`amout_paid`,
				  cr.`balance`,
				  cr.`date`,
				  cr.pay_type,
				  cr.`date_full_pay`,
				  cr.`time_full_pay`,
				  cr.pay_type,
				  o.id AS o_id,
				  o.`first_name` AS o_f_name,
				  o.`last_name` AS o_l_name,
				  o.`sex` AS o_sex,
				  o.`dob` AS o_dob,
				  o.`passport_name` AS o_passport,
				  o.`pass_issuedate` AS o_pass_issuedate,
				  o.`pass_expireddate` AS o_pass_expireddate,
				  o.`card_name` AS o_card_name,
				  o.`card_issuedate` AS o_card_issuedate,
				  o.`card_expireddate` AS o_card_expireddate,
				  o.`nationality` AS o_national,
				  o.`name_oncard` AS o_nameoncard,
				  o.`phone` AS o_phone,
				  o.`email` AS o_email,
				  c.`first_name`,
				  c.`last_name`,
				  c.`sex`,
				  c.`dob`,
				  c.`passport_name`,
				  c.`pass_issuedate`,
				  c.`pass_expireddate`,
				  c.`card_name`,
				  c.`card_issuedate`,
				  c.`card_expireddate`,
				  c.`nationality`,
				  c.`name_oncard`,
				  c.`phone`,
				  c.`email`,
				  v.`reffer`,
				  v.`frame_no`,
				  v.`licence_plate`,
				  (SELECT m.`title` FROM `ldc_make` AS m  WHERE m.`id` = v.`make_id`) AS make,
				  (SELECT m.`title` FROM `ldc_model` AS m WHERE m.`id` = v.`model_id`) AS model,
				  (SELECT s.`title` FROM `ldc_submodel` AS s WHERE s.`id` = v.`sub_model`) AS sub_model,
				  v.`year`,
				  v.`chassis_no`,
				  v.`engine_number`,
				  v.`of_axlex`,
				  v.`of_cylinder`,
				  v.`color`,
				  v.`cylinders_dip`,
				  (SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.`id` = v.`engine`) AS hors_power,
				  (SELECT t.`type` FROM `ldc_type` AS t WHERE t.`id` = v.`car_type`) AS car_type,
				  v.`licence_plate` 
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
				  AND cr.`id` =$id";
    	$row = $db->fetchRow($sql);
    	if($row){
    		return $row;
    	}
    }
    
    function getAllCarSaleAgreement(){
    	$db = $this->getAdapter();
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
				  a.`balance`
				FROM
				  `ldc_vehicle` AS v,
				  ldc_customer AS c,
				  ldc_customer AS o,
				  `ldc_car_sale_aggreement` AS a 
				WHERE a.`vehicle_id` = v.id 
				  AND a.`owner_id` = o.`id` 
				  AND a.`customer_id` = c.`id` ";
    	
    	$row=$db->fetchAll($sql);
    	if($row){
    		return $row;
    	}
    }
    
    function getAllSaleReciept(){
    	$db = $this->getAdapter();
    	$sql ="SELECT
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
				  AND a.`id`=cr.`sale_id`";
    	$row = $db->fetchAll($sql);
    	if($row){
    		return $row;
    	}
    }
}

