<?php

class Bookings_Model_DbTable_DbCustomerPayment extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getCarbookingPayment($data){
		$db = $this->getAdapter();
		$customer = $data['customer'];
		$sql=" SELECT cb.*,
			l.`location_name` AS from_location,
			tl.`location_name` AS to_location
			 FROM `ldc_carbooking` AS cb,
			 `ldc_package_location` AS l,
			`ldc_package_location` AS tl
			  WHERE 
			 l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`  
			 AND cb.`customer_id` =$customer  AND cb.`status`=1 AND cb.`is_customer_paid` =0";
		
		$from_date =(empty($data['start_date']))? '1': " cb.booking_date >= '".date("Y-m-d",strtotime($data['start_date']))." 00:00:00'";
		$to_date = (empty($data['end_date']))? '1': " cb.booking_date <= '".date("Y-m-d",strtotime($data['end_date']))." 23:59:59'";
		$sql.= " AND  ".$from_date." AND ".$to_date;
		$rs = $db->fetchAll($sql);
		
		$string='';
		$no = $data['keyindex'];
		$identity='';
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		if(!empty($rs)){
			foreach ($rs as $key => $row){
				if (empty($identity)){
					$identity=$no;
				}else{$identity=$identity.",".$no;
				}
				$string.='
				<tr id="row'.$no.'" style="background: #fff; border: solid 1px #bac; font-size: 11px;">
					<td align="center" style=" vertical-align: middle; padding: 0 10px;"><input  OnChange="CheckAllTotal('.$no.')" style=" vertical-align: top; height: initial;" type="checkbox" class="checkbox" id="mfdid_'.$no.'" value="'.$no.'"  name="selector[]"/></td>
					<td style="text-align: center;vertical-align: middle; ">'.($key+1).'</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
						<label id="billingdatelabel'.$no.'">'.date("d-M-Y",strtotime($row['booking_date'])).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="carbooking_id'.$no.'" id="carbooking_id'.$no.'" value="'.$row['id'].'" >
					</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
						<label id="invoicelabel'.$no.'">'.$row['booking_no'].' ('.$row['from_location'].' to '.$row['to_location'].')</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="bookingno_hidden'.$no.'" id="bookingno_hidden'.$no.'" value="'.$row['booking_no'].'" >
					</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
							<label id="origtotallabel'.$no.'">'.number_format($row['due'],2).'</label>
						</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
						<label id="duelabel'.$no.'">'.number_format($row['due_after'],2).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="due_after'.$no.'" id="due_after'.$no.'" value="'.$row['due_after'].'" >
					</td>
					<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
					<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['due_after'].'" style="text-align: center;" ></td>
				</tr>
				';$no++;
			}
		}else{
			$no++;
		}
		$array = array('stringrow'=>$string,'keyindex'=>$no,'identity'=>$identity);
		return $array;
	}
	
	function getCarbookingPaymentForEdit($data){
		
		$rows = $this->getCarbookingPaymentDetail($data['payment_id']);
		$listBookingpaid ='';
		if (!empty($rows)) foreach ($rows as $paymentdetail){
			if (empty($listBookingpaid)){
				$listBookingpaid=$paymentdetail['booking_id'];
			}else{$listBookingpaid=$listBookingpaid.",".$paymentdetail['booking_id'];
			}
		}
		
		$db = $this->getAdapter();
		$customer = $data['customer'];
		$sql=" SELECT cb.*,
		(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`from_location` LIMIT 1) AS from_location,
			(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`to_location` LIMIT 1) AS to_location
		FROM `ldc_carbooking` AS cb
		WHERE
		 cb.`customer_id` =$customer  AND cb.`status`=1 AND cb.`is_customer_paid` =0";
	
		$from_date =(empty($data['start_date']))? '1': " cb.booking_date >= '".date("Y-m-d",strtotime($data['start_date']))." 00:00:00'";
		$to_date = (empty($data['end_date']))? '1': " cb.booking_date <= '".date("Y-m-d",strtotime($data['end_date']))." 23:59:59'";
		$sql.= " AND  ".$from_date." AND ".$to_date;
		if (!empty($listBookingpaid)){
			$sql.=" OR cb.`id` IN ($listBookingpaid) ";
		}
		$rs = $db->fetchAll($sql);
	
		$string='';
		$no = $data['keyindex'];
		$identity='';
		$identityedit='';
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		if(!empty($rs)){
			foreach ($rs as $key => $row){
				if (empty($identity)){
					$identity=$no;
				}else{$identity=$identity.",".$no;
				}
				$rowpaymentdetail = $this->getCarbookingPaymentAndBookingId($data['payment_id'], $row['id']);
				if (!empty($rowpaymentdetail)){
					$duevalu=$rowpaymentdetail['due_amount'];
					$paymenttailByBooking = $this->getSumCarbookingPaymentDetailByBookingId($rowpaymentdetail['booking_id'], $rowpaymentdetail['id']);// get other pay amount on this Booking id on other payment number
					if (!empty($paymenttailByBooking)){
						$duevalu = $rowpaymentdetail['due']-$paymenttailByBooking['tolalpayamount'];
					}
					if (empty($identityedit)){
						$identityedit=$no;
					}else{
						$identityedit=$identityedit.",".$no;
					}
					$string.='
					<tr id="row'.$no.'" style="background: #fff; border: solid 1px #bac; font-size: 11px;">
						<td align="center" style=" vertical-align: middle; padding: 0 10px;"><input checked="checked"  OnChange="CheckAllTotal('.$no.')" style=" vertical-align: top; height: initial;" type="checkbox" class="checkbox" id="mfdid_'.$no.'" value="'.$no.'"  name="selector[]"/></td>
						<td style="text-align: center;vertical-align: middle; ">'.($key+1).'</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
							<label id="billingdatelabel'.$no.'">'.date("d-M-Y",strtotime($row['booking_date'])).'</label>
							<input type="hidden" dojoType="dijit.form.TextBox" name="carbooking_id'.$no.'" id="carbooking_id'.$no.'" value="'.$row['id'].'" >
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
							<label id="invoicelabel'.$no.'">'.$row['booking_no'].' ('.$row['from_location'].' to '.$row['to_location'].')</label>
							<input type="hidden" dojoType="dijit.form.TextBox" name="bookingno_hidden'.$no.'" id="bookingno_hidden'.$no.'" value="'.$row['booking_no'].'" >
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
							<label id="origtotallabel'.$no.'">'.number_format($rowpaymentdetail['due'],2).'</label>
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
							<label id="duelabel'.$no.'">'.number_format($row['due_after'],2).'</label>
							<input type="hidden" dojoType="dijit.form.TextBox" name="due_after'.$no.'" id="due_after'.$no.'" value="'.$duevalu.'" >
							<input type="hidden" dojoType="dijit.form.TextBox" name="detailid'.$no.'" id="detailid'.$no.'" value="'.$rowpaymentdetail['id'].'" >
						</td>
						<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="'.$rowpaymentdetail['paid'].'" style="text-align: center;" ></td>
						<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$rowpaymentdetail['due_after'].'" style="text-align: center;" ></td>
					</tr>';
				}else{
					$string.='
						<tr id="row'.$no.'" style="background: #fff; border: solid 1px #bac; font-size: 11px;">
							<td align="center" style=" vertical-align: middle; padding: 0 10px;"><input  OnChange="CheckAllTotal('.$no.')" style=" vertical-align: top; height: initial;" type="checkbox" class="checkbox" id="mfdid_'.$no.'" value="'.$no.'"  name="selector[]"/></td>
							<td style="text-align: center;vertical-align: middle; ">'.($key+1).'</td>
							<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
								<label id="billingdatelabel'.$no.'">'.date("d-M-Y",strtotime($row['booking_date'])).'</label>
								<input type="hidden" dojoType="dijit.form.TextBox" name="carbooking_id'.$no.'" id="carbooking_id'.$no.'" value="'.$row['id'].'" >
							</td>
							<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
								<label id="invoicelabel'.$no.'">'.$row['booking_no'].' ('.$row['from_location'].' to '.$row['to_location'].')</label>
								<input type="hidden" dojoType="dijit.form.TextBox" name="bookingno_hidden'.$no.'" id="bookingno_hidden'.$no.'" value="'.$row['booking_no'].'" >
							</td>
							<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
									<label id="origtotallabel'.$no.'">'.number_format($row['due'],2).'</label>
								</td>
							<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
								<label id="duelabel'.$no.'">'.number_format($row['due_after'],2).'</label>
								<input type="hidden" dojoType="dijit.form.TextBox" name="due_after'.$no.'" id="due_after'.$no.'" value="'.$row['due_after'].'" >
							</td>
							<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
							<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['due_after'].'" style="text-align: center;" ></td>
						</tr>';
				}
				$no++;
			}
		}else{
			$no++;
		}
		$array = array('stringrow'=>$string,'keyindex'=>$no,'identity'=>$identity,'identitycheck'=>$identityedit,);
		return $array;
	}
	function getSumCarbookingPaymentDetailByBookingId($booking_id,$detail_id){
		$db = $this->getAdapter();
		$sql="SELECT 
				SUM(pd.`paid`) AS tolalpayamount
			FROM `ldc_carbooking_payment` AS p,
				`ldc_carbooking_payment_detail` AS pd 
			WHERE 
				p.id = pd.`payment_id`
				AND pd.`id`!=$detail_id AND pd.`booking_id`=$booking_id 
				AND p.`status`=1
			LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getCarbookingPaymentAndBookingId($payment_id,$booking_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.*,
			(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_no,
			(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_date,
			(SELECT c.due FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS due,
			(SELECT c.due_after FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS due_after
			FROM `ldc_carbooking_payment_detail` AS pd 
			WHERE pd.`payment_id`=$payment_id AND pd.`booking_id`=$booking_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllCarbookingPayment($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT cb.id,cb.`payment_no`,
			CONCAT(c.`last_name`,'(',c.customer_code,')') AS customer,cb.`payment_date`,
			(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cb.`payment_method` LIMIT 1) AS `payment_method`,
			cb.`balance`,cb.`paid`,cb.`grand_total`,cb.`status`
		FROM 
			`ldc_carbooking_payment` AS cb,
			`ldc_customer` AS c
		WHERE 
			c.id = cb.`customer_id`
			AND cb.`status`>-1 AND cb.`payment_date`>='$from_date' AND cb.`payment_date`<='$to_date'
		";
		$where = '';
		if($search["search_text"] !=""){
			$s_where=array();
			$s_search=addslashes(trim($search['search_text']));
			$s_where[]=" CONCAT(c.`first_name`,' ',c.`last_name`) LIKE '%{$s_search}%'";
			$s_where[]=" cb.`payment_no` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`grand_total` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`paid` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`balance` LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($search['customer']>0){
			$where.=" AND cb.`customer_id`=".$search['customer'];
		}
		$order=' ORDER BY cb.id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function addCarbookingPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$reciept_no = $_db->getNewPaymentNO();
			$_arr_payment=array(
					'payment_no'	  => $reciept_no,
					'customer_id'	  => $_data['customer'],
					'payment_date'	  => $_data['payment_date'],
					'payment_type'	  => 0,
					'payment_method'  => $_data['payment_method'],
					'paid'	  		  => $_data['total_paid'],
					'balance'	      => $_data['balance'],
					'grand_total'	  => $_data['total_due'],
// 					'amount'      => $_data['amount'],
					'note'	  => $_data['remark'],
					'status'	  => 1,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_carbooking_payment";
			$id_payment = $this->insert($_arr_payment);
			
			$ids = explode(',', $_data['identity']);
			$dueafter=0;
			foreach ($ids as $i){
				$is_payment =0;
				$booking = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid = $_data['payment_amount'.$i];
				
				if (!empty($booking)){
					$dueafter =$booking['due_after']-$paid;
					if ($dueafter>0){
						$is_payment=0;
					}else{
						$is_payment=1;
					}
					$array=array(
							'is_customer_paid'=>$is_payment,
							'due_after'=>$dueafter,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				
				$arrs = array(
						'payment_id'=>$id_payment,
						'booking_id'=>$_data['carbooking_id'.$i],
						'due_amount'=>$_data['due_after'.$i],
						'paid'=>$_data['payment_amount'.$i],
						'remain'=>$_data['remain'.$i],
				);
				$this->_name ='ldc_carbooking_payment_detail';
				$this->insert($arrs);
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	public function updateCarbookingPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
// 			$reciept_no = $_db->getNewCommissionPaymentNO();
			$_arr_payment=array(
					'customer_id'	  => $_data['customer'],
					'payment_date'	  => $_data['payment_date'],
					'payment_type'	  => 0,
					'payment_method'	  => $_data['payment_method'],
					'paid'	  => $_data['total_paid'],
					'balance'	  => $_data['balance'],
					'grand_total'	  => $_data['total_due'],
// 					'amount'      => $_data['amount'],
					'note'	  => $_data['remark'],
					'status'	  => 1,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_carbooking_payment";
			$where = ' id = '.$_data['payment_id'];
			$this->update($_arr_payment, $where);
			$id_payment = $_data['payment_id'];
			
			$row = $this->getCarbookingPaymentDetail($id_payment);
			if (!empty($row)) foreach ($row as $pay_detail){
				$rowpaymentdetail = $this->getCarbookingPaymentAndBookingId($_data['payment_id'], $pay_detail['booking_id']);
				
				if (!empty($rowpaymentdetail)){
					$bookingafter = $this->getCarbookingById($pay_detail['booking_id']);
					$duevalu=$rowpaymentdetail['paid'];
					
					$paymenttailByBooking = $this->getSumCarbookingPaymentDetailByBookingId($pay_detail['booking_id'], $pay_detail['id']);// get other pay amount on this Booking on other commission payment
					$dueafters = $bookingafter['due_after']+$duevalu;
					if (!empty($paymenttailByBooking['tolalpayamount'])){
						$duevalu = ($rowpaymentdetail['due']-$paymenttailByBooking['tolalpayamount']);
						$dueafters =$duevalu;
					}
					
					if ($dueafters>0){
						$is_payments=0;
					}else{
						$is_payments=1;
					}
					$array=array(
							'is_customer_paid'=>$is_payments,
							'due_after'=>$dueafters,
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
			$this->_name="ldc_carbooking_payment_detail";
			$where2=" payment_id = ".$id_payment;
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
					$dueafter =$booking['due_after']-$paid;
					if ($dueafter>0){
						$is_payment=0;
					}else{
						$is_payment=1;
					}
					$array=array(
							'is_customer_paid'=>$is_payment,
							'due_after'=>$dueafter,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				if (!empty($_data['detailid'.$i])){
					$arrs = array(
							'payment_id'=>$id_payment,
							'booking_id'=>$_data['carbooking_id'.$i],
							'due_amount'=>$_data['due_after'.$i],
							'paid'=>$_data['payment_amount'.$i],
							'remain'=>$_data['remain'.$i],
					);
					$this->_name ='ldc_carbooking_payment_detail';
					$where12 =" id= ".$_data['detailid'.$i];
					$this->update($arrs, $where12);
				}else{
					$arrs = array(
							'payment_id'=>$id_payment,
							'booking_id'=>$_data['carbooking_id'.$i],
							'due_amount'=>$_data['due_after'.$i],
							'paid'=>$_data['payment_amount'.$i],
							'remain'=>$_data['remain'.$i],
					);
					$this->_name ='ldc_carbooking_payment_detail';
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
	function getCarbookingPaymentByID($id){
		$db = $this->getAdapter();
		$sql="SELECT cp.* FROM `ldc_carbooking_payment` AS cp WHERE cp.`id`=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getCarbookingPaymentDetail($payment_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.* FROM `ldc_carbooking_payment_detail` AS pd WHERE pd.`payment_id` =$payment_id";
		return $db->fetchAll($sql);
	}
}
?>