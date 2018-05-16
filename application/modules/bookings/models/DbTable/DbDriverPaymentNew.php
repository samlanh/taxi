<?php

class Bookings_Model_DbTable_DbDriverPaymentNew extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_agencyclear_payment";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	 
	function getAllDriverClearPayment($search){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$db = $this->getAdapter();
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$print=$tr->translate("PRINT");
		$edit=$tr->translate("EDIT");
		$sql=" SELECT d.id,d.`payment_no`,
				 
				(SELECT CONCAT(n.`last_name`,'(',n.`driver_id`,')') 
 	           FROM `ldc_driver` AS n WHERE n.`status` =1 AND n.id=d.`driver_id` LIMIT 1) AS driver_name,
 	           DATE_FORMAT(d.`payment_date`, '%d-%b-%Y')As payment_date, (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=d.`payment_method` LIMIT 1) AS `payment_method`,
		       d.`total_driver_fee`,d.`total_driver_recived`,d.`paid_driver` ,
		      (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=12 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
		      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name,d.`status`,d.`status`,'$print'
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
		$order=' ORDER BY d.id DESC';
		return $db->fetchAll($sql.$where);
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
					
					'fil_start_date'  	  => $_data['fil_start_date'],
					'fil_end_date'  	  => $_data['fil_end_date'],
					
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
	
	function getAllAgentcyBooking($id,$type,$data){
		//return $data['fil_start_date'].'aaa';
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
		$date='';
		if($type==1){
			$and=" AND cb.id=".$id;
		}else{
			$and=" AND cb.driver_id=".$id;
			if(!empty($data['fil_start_date'])){
				$date="  AND cb.`delivey_date` BETWEEN '".$data['fil_start_date']."' AND '".$data['fil_end_date']."'";
			}
		}
		$order="  ORDER BY cb.`delivey_date`,cb.`booking_no` ASC ";
		return $db->fetchAll($sql.$and.$date.$order);
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
        cd.`status`,fil_start_date,fil_end_date
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
		$order=" ORDER BY cb.`delivey_date`,cb.`booking_no` ASC ";
		return $db->fetchAll($sql.$order);
	}
	 
}
?>