<?php

class Bookings_Model_DbTable_DbAgentcyPayment extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_agencyclear_payment";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	 
	function getAllAgencyPayment($search){
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
		       (SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=12 AND v.`key_code`=d.`paid_type` LIMIT 1) AS `paid_type`,
		      (SELECT u.`first_name` FROM `rms_users` AS u WHERE u.id=d.`user_id` LIMIT 1 )AS user_name,d.`status`
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
		$order=' ORDER BY d.id DESC ';
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function addAgencyPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$agency=$_data['agency_old'];
			$invoice='';
			if(empty($_data['agency'])){
				$_data['agency']=$agency;
			}
			if(empty($_data['invoice'])){
				$_data['invoice']=$invoice;
			}
			
			$_gency=array(
					'payment_no'	  => $_data['reciept_no'],
					'agency_id'	  	  => $_data['agency'],
					'payment_date'	  => $_data['payment_date'],
					'payment_method'  => $_data['payment_by'],
					'payment_type'	  => $_data['payment_method'],
					'note'  		  => $_data['remark'],
					'create_date'	  => date("Y-m-d H:i:s"),
					'modify_date'  	  => date("Y-m-d H:i:s"),
					
					'total_commission'    => $_data['total_commission_fee'],
					'total_agen_recived'  => $_data['total_agen_recived'],
					'paid_agen'      	  => $_data['paid_agen'],
					
					'agency_paid'      	  => $_data['agency_paid'],
					'agency_balance'      => $_data['agency_balance'],
					'total_alls'      	  => $_data['total_alls'],
					'paid_type'       => $_data['paid_type'],
					'status'      	  => $_data['status'],
					'user_id'      	  => $this->getUserId(),
			);
			$this->_name="ldc_agencyclear_payment";
			$agen_id = $this->insert($_gency);
			
			$ids = explode(',', $_data['record_row']);
			$commission=0;
			$paid_after=0;
			$balance_after=0;
			foreach ($ids as $i){
				$is_agency_paid =0;
				 
				$commission = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid=$this->getAgencyPaidById($_data['carbooking_id'.$i]);
				$balance=$this->getAgencyBalanceById($_data['carbooking_id'.$i]);
				if (!empty($commission)){
					$dueafter =$commission['commision_fee_after']-$_data['gency_fee_'.$i];
					if ($dueafter>0){
						$is_agency_paid=0;
					}else{
						$is_agency_paid=1;
					}
					if(!empty($paid)){
						$paid_after=$paid['paid_after']-$_data['paid_after_'.$i];
					}
					if(!empty($balance)){
						$balance_after=$balance['balance_after']-$_data['balance_after_'.$i];
					}
					
					$array=array(
							'is_paid_commission'=>$is_agency_paid,
							'commision_fee_after'=>$dueafter,
							'paid_after'		=>$paid_after,
							'balance_after'		=>$balance_after,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				
				$arrs = array(
						'clearagency_id'=>$agen_id,
						'booking_id'	=>$_data['carbooking_id'.$i],
						'gency_fee'		=>$_data['gency_fee_'.$i],
						'conpany_price'	=>$_data['conpany_price_'.$i],
						'paid'			=>$_data['paid_after_'.$i],
						'balance'		=>$_data['balance_after_'.$i],
						'all_total'		=>$_data['all_total_'.$i],
						'paid_status'	=>$_data['paid_status_'.$i],
						'balance_satatus'=>$_data['balance_status_'.$i],
						'is_clear'		=>1,
						'user_id'      	=> $this->getUserId(),
						'status'		=>1,
				);
				$this->_name ='ldc_agencyclear_payment_detail';
				$this->insert($arrs);
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	public function updateCommissionPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
// 			$reciept_no = $_db->getNewCommissionPaymentNO();
			$_arrcommission=array(
// 					'payment_no'	  => $reciept_no,
					'agency_id'	  => $_data['agency'],
					'payment_date'	  => $_data['payment_date'],
					'payment_type'	  => 0,
					'payment_method'	  => $_data['payment_method'],
					'paid'	  => $_data['total_paid'],
					'balance'	  => $_data['balance'],
					'total_due'	  => $_data['total_due'],
					'amount'      => $_data['amount'],
					'note'	  => $_data['remark'],
					'status'	  => 1,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_commission_payment";
			$where = ' id = '.$_data['payment_id'];
			$this->update($_arrcommission, $where);
			$id_commission_payment = $_data['payment_id'];
			
			$row = $this->getCommissionPaymentDetail($id_commission_payment);
			if (!empty($row)) foreach ($row as $pay_detail){
				$rowpaymentdetail = $this->getCommissionPaymentAndBookingId($_data['payment_id'], $pay_detail['booking_id']);
				
				if (!empty($rowpaymentdetail)){
					$bookingafter = $this->getCarbookingById($pay_detail['booking_id']);
					$duevalu=$rowpaymentdetail['paid'];
					
					$paymenttailByBooking = $this->getSumCommissionPaymentDetailByBookingId($pay_detail['booking_id'], $pay_detail['id']);// get other pay amount on this Booking on other commission payment
					$dueafters = $bookingafter['commision_fee_after']+$duevalu;
					if (!empty($paymenttailByBooking['tolalpayamount'])){
						$duevalu = ($rowpaymentdetail['commision_fee']-$paymenttailByBooking['tolalpayamount']);
						$dueafters =$duevalu;
					}
					
					if ($dueafters>0){
						$is_payments=0;
					}else{
						$is_payments=1;
					}
					$array=array(
							'is_paid_commission'=>$is_payments,
							'commision_fee_after'=>$dueafters,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$pay_detail['booking_id'];
					$this->update($array, $where);
				}
			}
			
			$ids = explode(',', $_data['identity']);
			$detailidlist = '';
			foreach ($ids as $i){
				if (empty($detailidlist)){
					if (!empty($_data['detailid'.$i])){
						$detailidlist= $_data['detailid'.$i];
					}
				}else{
					if (!empty($_data['detailid'.$i])){
						$detailidlist = $detailidlist.",".$_data['detailid'.$i];
					}
				}
			}
			// delete old payment detail that don't have on new payment detail after edit
			$this->_name="ldc_commission_payment_detail";
			$where2=" commission_payment_id = ".$id_commission_payment;
			if (!empty($detailidlist)){ // check if has old payment detail  detail id
				$where2.=" AND id NOT IN (".$detailidlist.")";
			}
			$this->delete($where2);
			
			$dueafter=0;
			foreach ($ids as $i){
				$is_payment =0;
				$booking = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid = $_data['payment_amount'.$i];
	
				if (!empty($booking)){
					$dueafter =$booking['commision_fee_after']-$paid;
					if ($dueafter>0){
						$is_payment=0;
					}else{
						$is_payment=1;
					}
					$array=array(
							'is_paid_commission'=>$is_payment,
							'commision_fee_after'=>$dueafter,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				if (!empty($_data['detailid'.$i])){
					$arrs = array(
							'commission_payment_id'=>$id_commission_payment,
							'booking_id'=>$_data['carbooking_id'.$i],
							'due_amount'=>$_data['commision_fee'.$i],
							'paid'=>$_data['payment_amount'.$i],
							'remain'=>$_data['remain'.$i],
					);
					$this->_name ='ldc_commission_payment_detail';
					$where12 =" id= ".$_data['detailid'.$i];
					$this->update($arrs, $where12);
				}else{
					$arrs = array(
							'commission_payment_id'=>$id_commission_payment,
							'booking_id'=>$_data['carbooking_id'.$i],
							'due_amount'=>$_data['commision_fee'.$i],
							'paid'=>$_data['payment_amount'.$i],
							'remain'=>$_data['remain'.$i],
					);
					$this->_name ='ldc_commission_payment_detail';
					$this->insert($arrs);
				}
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
		$sql="SELECT c.paid_after FROM `ldc_carbooking` AS c WHERE c.`id` = $id AND c.`paid_status`=2  LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	function getAgencyBalanceById($id){
		$db = $this->getAdapter();
		$sql="SELECT c.`balance_after` FROM `ldc_carbooking` AS c WHERE c.`id` = $id AND c.`balance_status`=2  LIMIT 1 ";
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
        	  cb.commision_fee_after,cb.grand_total AS total,cb.grand_total_after AS total_after,cb.paid_after,cb.balance_after,cb.paid_status,cb.balance_status,
        	  cb.paid_status,cb.balance_status,
        	  COALESCE((SELECT ".$array[$lang]." FROM tb_view AS v WHERE v.key_code=cb.paid_status AND v.type=18 AND cb.paid_after!=0 LIMIT 1),'') AS status_paid,
        	  COALESCE((SELECT ".$array[$lang]." FROM tb_view AS v WHERE v.key_code=cb.balance_status AND v.type=19 AND cb.balance_after!=0 LIMIT 1),'') AS status_balance,
        	  cb.`is_paid_commission`,cb.`agency_id`
			FROM  ldc_carbooking AS cb,ldc_customer AS c
			WHERE cb.customer_id=c.id 
			AND cb.is_paid_commission=0";
		$and='';
		if($type==1){
			$and=" AND cb.id=".$id;
		}else{
			$and=" AND cb.agency_id=".$id;
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
			 AND cb.balance_after>0 ";
		if($type==1){
			$and=" AND cb.id=$agency_id";
		}else{
			$and=" AND cb.agency_id=$agency_id";
		}
		return $db->fetchRow($sql.$and);
	}
	
}
?>