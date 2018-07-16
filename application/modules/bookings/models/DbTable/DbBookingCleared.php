<?php

class Bookings_Model_DbTable_DbBookingCleared extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_agencyclear_payment";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	 
	function getAllBookingClearedPayment($search=null){
		$db = $this->getAdapter();
		$glob=new Application_Model_DbTable_DbGlobal();
		$lang= $glob->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
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
			cb.`is_paid_commission`,cb.`is_paid_to_driver`=1
			FROM `ldc_carbooking` AS cb,
			`ldc_package_location` AS l,
			`ldc_package_location` AS tl,
			ldc_customer AS c
			WHERE 
			c.id=cb.customer_id   
			AND l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`
		    AND cb.`is_paid_commission`=1 
			AND cb.`is_paid_to_driver`=1 ";
		$where = '';
		
		if($search['date_type']==2){
			$from_date =(empty($search['from_book_date']))? '1': "cb.`delivey_date` >= '".$search['from_book_date']." 00:00:00'";
			$to_date = (empty($search['to_book_date']))? '1': "cb.`delivey_date` <= '".$search['to_book_date']." 23:59:59'";
			
			$from_time =(empty($search['start_time']))? '1': "cb.`delivey_time` >= '".$search['start_time']." 00:00:00'";
			$to_time = (empty($search['delivery_time']))? '1': "cb.`delivey_time` <= '".$search['delivery_time']." 23:59:59'";
			
			$order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
		}
		if($search['date_type']==1){
			$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
			$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
			$order=' ORDER BY cb.`booking_date`,cb.delivey_time ASC';
		}
		$where = "  AND ".$from_date." AND ".$to_date." AND ".$from_time." AND ".$to_time;
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
		// echo $sql.$where.$order;
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
	
	public function addDriverPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$agency=$_data['driver_old'];;
			$invoice='';
			if(empty($_data['driver'])){
				$_data['driver']=$agency;
			}
			if(empty($_data['invoice'])){
				$_data['invoice']=$invoice;
			}
			
			$_gency=array(
					'payment_no'	  => $_data['reciept_no'],
					'driver_id'	  	  => $_data['driver'],
					'payment_date'	  => $_data['payment_date'],
					'payment_method'  => $_data['payment_by'],
					'payment_type'	  => $_data['payment_method'],
					'note'  		  => $_data['remark'],
					'create_date'	  => date("Y-m-d H:i:s"),
					'modify_date'  	  => date("Y-m-d H:i:s"),
					
					'total_driver_fee'      => $_data['total_commission_fee'],
					'total_driver_recived'  => $_data['total_agen_recived'],
					'paid_driver'      	  	=> $_data['paid_agen'],
					
					'driver_paid'      	  	=> $_data['agency_paid'],
					'driver_balance'      	=> $_data['agency_balance'],
					'total_alls'      	  	=> $_data['total_alls'],
					'total_profit'			=> ($_data['total_alls'])-($_data['total_commission_fee']),
					'paid_type'       		=> $_data['paid_type'],
					'status'      	  		=> $_data['status'],
					'user_id'      	  		=> $this->getUserId(),
			);
			$this->_name="ldc_driverclear_payment";
			$driver_id = $this->insert($_gency);
			
			$ids = explode(',', $_data['record_row']);
			$driver_fee=0;
			$paid_after=0;
			$balance_after=0;
			foreach ($ids as $i){
				$is_driver_paid =0;
				 
				$driver_fee = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid=$this->getAgencyPaidById($_data['carbooking_id'.$i]);
			//	$balance=$this->getAgencyBalanceById($_data['carbooking_id'.$i]);
				$array=array();
				if (!empty($driver_fee)){
					$dueafter =$driver_fee['driver_fee_after']-$_data['gency_fee_'.$i];
					if ($dueafter>0){
						$is_driver_paid=0;
					}else{
						$is_driver_paid=1;
					}
					if(!empty($paid)){
						$paid_after=$paid['paid_after']-$_data['paid_after_'.$i];
						$array['paid_after']=$paid_after;
					}
// 					if(!empty($balance)){
// 						$balance_after=$balance['balance_after']-$_data['balance_after_'.$i];
// 						$array['balance_after']=$balance_after;
// 					}
					$array['is_paid_to_driver']=$is_driver_paid;
					$array['driver_fee_after']=$dueafter;
					
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				
				$arrs = array(
						'driverclear_id'=>$driver_id,
						'booking_id'	=>$_data['carbooking_id'.$i],
						'driver_fee'	=>$_data['gency_fee_'.$i],
						//'conpany_price'	=>$_data['conpany_price_'.$i],
						'paid'			=>$_data['paid_after_'.$i],
						//'balance'		=>$_data['balance_after_'.$i],
				        'note'			=>$_data['note_'.$i],
						'all_total'		=>$_data['all_total_'.$i],
						'paid_status'	=>$_data['paid_status_'.$i],
						'balance_satatus'=>$_data['balance_status_'.$i],
						'is_clear'		=>1,
						'user_id'      	=> $this->getUserId(),
						'status'		=>1,
				);
				$this->_name ='ldc_driverclear_payment_detail';
				$this->insert($arrs);
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	public function updateDriverPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$agency=$_data['driver_old'];;
			$invoice='';
			if(empty($_data['driver'])){
				$_data['driver']=$agency;
			}
			if(empty($_data['invoice'])){
				$_data['invoice']=$invoice;
			}
			
			$row_detail=$this->getDriverClearDetail($_data['hidden_id']);
			if(!empty($row_detail)){
				foreach ($row_detail As $row){
					$driver_fee = $this->getCarbookingById($row['booking_id']);
					$paid=$this->getAgencyPaidById($row['booking_id']);
					$array=array();
					if (!empty($driver_fee)){
						$dueafter =$driver_fee['driver_fee_after']+$row['driver_fee'];
						if(!empty($paid)){
							$paid_after=$paid['paid_after']+$row['paid'];
							$array['paid_after']=$paid_after;
						}
						$array['is_paid_to_driver']=0;
						$array['driver_fee_after']=$dueafter;
						$this->_name="ldc_carbooking";
						$where = " id =".$row['booking_id'];
						$this->update($array, $where);
					}
				}
			}
			
				
			$_gency=array(
					'payment_no'	  => $_data['reciept_no'],
					'driver_id'	  	  => $_data['driver'],
					'payment_date'	  => $_data['payment_date'],
					'payment_method'  => $_data['payment_by'],
					'payment_type'	  => $_data['payment_method'],
					'note'  		  => $_data['remark'],
					'create_date'	  => date("Y-m-d H:i:s"),
					'modify_date'  	  => date("Y-m-d H:i:s"),
						
					'total_driver_fee'      => $_data['total_commission_fee'],
					'total_driver_recived'  => $_data['total_agen_recived'],
					'paid_driver'      	  	=> $_data['paid_agen'],
						
					'driver_paid'      	  	=> $_data['agency_paid'],
					'driver_balance'      	=> $_data['agency_balance'],
					'total_alls'      	  	=> $_data['total_alls'],
					'total_profit'			=> ($_data['total_alls'])-($_data['total_commission_fee']),
					'paid_type'       		=> $_data['paid_type'],
					'status'      	  		=> $_data['status'],
					'user_id'      	  		=> $this->getUserId(),
			);
			$this->_name="ldc_driverclear_payment";
			$where=" id=".$_data['hidden_id'];  
		    $this->update($_gency, $where);
		    
		    $sql = "DELETE FROM ldc_driverclear_payment_detail WHERE driverclear_id=".$_data['hidden_id'];  
		    $db->query($sql);
				
			$ids = explode(',', $_data['record_row']);
			$driver_fee=0;
			$paid_after=0;
			$balance_after=0;
			foreach ($ids as $i){
				$is_driver_paid =0;
					
				$driver_fee = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid=$this->getAgencyPaidById($_data['carbooking_id'.$i]);
				//	$balance=$this->getAgencyBalanceById($_data['carbooking_id'.$i]);
				$array=array();
				if (!empty($driver_fee)){
					$dueafter =$driver_fee['driver_fee_after']-$_data['gency_fee_'.$i];
					if ($dueafter>0){
						$is_driver_paid=0;
					}else{
						$is_driver_paid=1;
					}
					if(!empty($paid)){
						$paid_after=$paid['paid_after']-$_data['paid_after_'.$i];
						$array['paid_after']=$paid_after;
					}
					// 					if(!empty($balance)){
					// 						$balance_after=$balance['balance_after']-$_data['balance_after_'.$i];
					// 						$array['balance_after']=$balance_after;
					// 					}
					$array['is_paid_to_driver']=$is_driver_paid;
					$array['driver_fee_after']=$dueafter;
						
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
	
				$arrs = array(
						'driverclear_id'=>$_data['hidden_id'],
						'booking_id'	=>$_data['carbooking_id'.$i],
						'driver_fee'	=>$_data['gency_fee_'.$i],
						//'conpany_price'	=>$_data['conpany_price_'.$i],
						'paid'			=>$_data['paid_after_'.$i],
						//'balance'		=>$_data['balance_after_'.$i],
						'note'			=>$_data['note_'.$i],
						'all_total'		=>$_data['all_total_'.$i],
						'paid_status'	=>$_data['paid_status_'.$i],
						'balance_satatus'=>$_data['balance_status_'.$i],
						'is_clear'		=>1,
						'user_id'      	=> $this->getUserId(),
						'status'		=>1,
				);
				$this->_name ='ldc_driverclear_payment_detail';
				$this->insert($arrs);
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	function getCarbookingById($id){
		$db = $this->getAdapter();
		$sql="SELECT c.* FROM `ldc_carbooking` AS c WHERE c.`id` = $id LIMIT 1";
		return $db->fetchRow($sql);
	}
	
	function getAgencyPaidById($id){
		$db = $this->getAdapter();
		$sql="SELECT c.paid_after FROM `ldc_carbooking` AS c WHERE c.`id` = $id AND c.`paid_status`=1  LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	function getAgencyBalanceById($id){
		$db = $this->getAdapter();
		$sql="SELECT c.`balance_after` FROM `ldc_carbooking` AS c WHERE c.`id` = $id AND c.`balance_status`=1  LIMIT 1 ";
		return $db->fetchRow($sql);
	}

	function getCommissionPaymentByID($id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_commission_payment` AS c WHERE c.`id` =$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getCommissionPaymentDetail($commission_payment_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.* FROM `ldc_commission_payment_detail` AS pd WHERE pd.`commission_payment_id`=$commission_payment_id";
		return $db->fetchAll($sql);
	}
	
	function getAllAgentcyBooking($id,$type){
		$db=$this->getAdapter();
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT cb.id,cb.payment_booking_no,booking_no,c.last_name,DATE_FORMAT(cb.booking_date,'%d-%b-%Y') AS booking_date,cb.customer_id,cb.`price`,
        	  cb.grand_total AS total,cb.grand_total_after AS total_after,cb.paid_after,cb.balance_after,cb.paid_status,cb.balance_status,
        	  cb.paid_status,cb.balance_status,
        	  COALESCE((SELECT ".$array[$lang]." FROM tb_view AS v WHERE v.key_code=cb.paid_status AND v.type=18 AND cb.paid_after!=0 LIMIT 1),'') AS status_paid,
        	  COALESCE((SELECT ".$array[$lang]." FROM tb_view AS v WHERE v.key_code=cb.balance_status AND v.type=19 AND cb.balance_after!=0 LIMIT 1),'') AS status_balance,
        	  cb.`driver_fee_after`, cb.`is_paid_to_driver`,cb.`driver_id`,
        	  
        	  cb.booking_no,DATE_FORMAT(cb.`delivey_date`, '%d-%b-%Y') AS date_delivey,TIME_FORMAT(cb.`delivey_time`,'%H:%i')AS `time`,
        	  (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=cb.`from_location`) AS from_loc ,
        	  (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=cb.`to_location`) AS to_loc ,
        	  (SELECT c.`title` FROM `ldc_vechicletye` AS c WHERE c.id=cb.`vehicletype_id`) AS car_type ,
        	  cb.`paid`,cb.`driver_fee`
        	  
			  FROM  ldc_carbooking AS cb,ldc_customer AS c
			  WHERE cb.customer_id=c.id 
			  AND cb.is_paid_to_driver=0 AND cb.`status_working`=1 AND cb.status =1 ";
		$and='';
		if($type==1){
			$and=" AND cb.id=".$id;
		}else{
			$and=" AND cb.driver_id=".$id;
		}
		return $db->fetchAll($sql.$and);
	}
	
	function getAgencyPayment($agency_id,$row_id,$type){
		$db=$this->getAdapter();
		$sql="SELECT  cb.id,
        	 (SELECT SUM(c.paid_after) FROM `ldc_carbooking` AS c WHERE c.paid_status=2 		AND c.id  IN (".$row_id.") )   AS agency_paid,
        	 (SELECT SUM(c.balance_after) FROM `ldc_carbooking` AS c WHERE c.balance_status=2 	AND c.id  IN (".$row_id.") )   AS agency_balance,
			 (SELECT SUM(b.paid_after) FROM `ldc_carbooking`AS b WHERE b.paid_status=1 			AND b.id  IN (".$row_id.") )   AS driver_paid ,
			 (SELECT SUM(b.balance_after) FROM `ldc_carbooking`AS b WHERE b.balance_status=1 	AND b.id  IN (".$row_id.") )   AS driver_balance
			 FROM  ldc_carbooking AS cb,ldc_customer AS c
			 WHERE cb.customer_id=c.id
			 ";
		$and='';
		if($type==1){
			$and=" AND cb.id=".$agency_id;
		}else{
			$and=" AND cb.driver_id=$agency_id";
		}
		return $db->fetchRow($sql.$and);
	}
	
	function getDriverClearById($id){
		$db = $this->getAdapter();
		$sql=" SELECT cd.id,cd.`payment_method`,cdd.`booking_id`,cd.`payment_no`,cd.driver_id,cd.payment_date,cd.payment_type,cd.note,cd.total_driver_fee,
	    cd.total_driver_recived,cd.paid_driver,cd.total_alls,cd.total_profit,cd.paid_type,cd.driver_paid,
        cd.`status`
        FROM `ldc_driverclear_payment` AS cd ,`ldc_driverclear_payment_detail` AS cdd
        WHERE  cd.`id`=cdd.`driverclear_id` AND cd.id=$id GROUP BY cd.`id`";
		return $db->fetchRow($sql);
	}
	
	function getDriverClearDetail($id){
		$db = $this->getAdapter();
		$sql=" SELECT   cb.`customer_id`,cdd.`booking_id`,cb.booking_no,DATE_FORMAT(cb.`delivey_date`, '%d-%b-%Y') AS date_delivey,TIME_FORMAT(cb.`delivey_time`,'%H:%i')AS `time`,
        (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=cb.`from_location`) AS from_loc ,
        (SELECT l.`location_name` FROM `ldc_package_location` AS l WHERE l.id=cb.`to_location`) AS to_loc ,
        (SELECT c.`title` FROM `ldc_vechicletye` AS c WHERE c.id=cb.`vehicletype_id`) AS car_type ,
        cdd.`all_total`,cdd.`driver_fee`, cdd.`paid`,cdd.`note`,cdd.`paid_status`,cdd.`balance_satatus`
        FROM `ldc_driverclear_payment` AS cd ,`ldc_driverclear_payment_detail` AS cdd,`ldc_carbooking` AS cb
        WHERE  cd.`id`=cdd.`driverclear_id` AND cd.status=1
        AND cb.`id`=cdd.`booking_id`
        AND cdd.`driverclear_id`=$id";
		return $db->fetchAll($sql);
	}
	
	function getAllIncomeBooking($search=null){
		$db = $this->getAdapter();
		$glob=new Application_Model_DbTable_DbGlobal();
		$lang= $glob->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
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
		cb.`is_paid_commission`,cb.`is_paid_to_driver`=1
		FROM `ldc_carbooking` AS cb,
		`ldc_package_location` AS l,
		`ldc_package_location` AS tl,
		ldc_customer AS c
		WHERE
		c.id=cb.customer_id
		AND l.`id` = cb.`from_location`
		AND tl.`id` = cb.`to_location`
		AND cb.`is_paid_to_driver`=1 ";
		$where = '';
	
		if($search['date_type']==2){
		$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
		$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
			
		$from_time =(empty($search['start_time']))? '1': "cb.`booking_date` >= '".$search['start_time']." 00:00:00'";
		$to_time = (empty($search['delivery_time']))? '1': "cb.`booking_date` <= '".$search['delivery_time']." 23:59:59'";
			
		$order=' ORDER BY cb.`booking_date`,cb.delivey_time ASC';
		}
		if($search['date_type']==1){
		$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
		$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
		$order=' ORDER BY cb.`booking_date`,cb.delivey_time ASC';
		}
		$where = "  AND ".$from_date." AND ".$to_date." AND ".$from_time." AND ".$to_time;
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
		// echo $sql.$where.$order;
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
	
		$order=' ORDER BY cb.`booking_date`,cb.delivey_time ASC';
			//echo $sql.$where.$order;
			return $db->fetchAll($sql.$where.$order);
	}
	
	 
}
?>