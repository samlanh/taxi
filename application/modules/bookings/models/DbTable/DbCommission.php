<?php

class Bookings_Model_DbTable_DbCommission extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getAgencyInfor($client_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT c.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =c.`sex` LIMIT 1) AS sexs
		FROM ldc_agency AS c WHERE id=".$client_id;
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
	
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$images = $baseurl."/images/profile.jpg";
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<ul class="list-unstyled">
					<li>
						<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value"></span>
					</li>
				</ul>
			</div>
			';
		if (!empty($row)){
			if (!empty($row['photo'])){
				$images = $baseurl."/images/agent/".$row['photo'];
			}
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<ul class="list-unstyled">
					<li>
						<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value">'.$row['first_name'].' '.$row['last_name'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value">'.$row['sexs'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value">'.$row['nationality'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['phone'].'</span>
					</li>
				</ul>
			</div>
			';
		}
		return $string;
	}
	function getCarbookingCommissionAgent($data){
		$db = $this->getAdapter();
		$agent = $data['agency'];
		$sql=" SELECT cb.*,
			l.`location_name` AS from_location,
			tl.`location_name` AS to_location
			 FROM `ldc_carbooking` AS cb,
			 `ldc_package_location` AS l,
			`ldc_package_location` AS tl
			  WHERE 
			 l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`  
			 AND cb.`agency_id` =$agent  AND cb.`status`=1 AND cb.`is_paid_commission` =0";
		
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
							<label id="origtotallabel'.$no.'">'.number_format($row['commision_fee'],2).'</label>
						</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
						<label id="duelabel'.$no.'">'.number_format($row['commision_fee_after'],2).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$row['commision_fee_after'].'" >
					</td>
					<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
					<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['commision_fee_after'].'" style="text-align: center;" ></td>
				</tr>
				';$no++;
			}
		}else{
			$no++;
		}
		$array = array('stringrow'=>$string,'keyindex'=>$no,'identity'=>$identity);
		return $array;
	}
	
	function getCarbookingCommissionAgentForEdit($data){
		
		$rows = $this->getCommissionPaymentDetail($data['payment_id']);
		$listBookingpaid ='';
		if (!empty($rows)) foreach ($rows as $paymentdetail){
			if (empty($listBookingpaid)){
				$listBookingpaid=$paymentdetail['booking_id'];
			}else{$listBookingpaid=$listBookingpaid.",".$paymentdetail['booking_id'];
			}
		}
		
		$db = $this->getAdapter();
		$agent = $data['agency'];
		$sql=" SELECT cb.*,
			(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`from_location` LIMIT 1) AS from_location,
			(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`to_location` LIMIT 1) AS to_location
			FROM `ldc_carbooking` AS cb
			WHERE cb.`status`=1 AND cb.`is_paid_commission` =0 AND cb.`agency_id` =$agent ";
	
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
				$rowpaymentdetail = $this->getCommissionPaymentAndBookingId($data['payment_id'], $row['id']);
				if (!empty($rowpaymentdetail)){
					$duevalu=$rowpaymentdetail['due_amount'];
					$paymenttailByBooking = $this->getSumCommissionPaymentDetailByBookingId($rowpaymentdetail['booking_id'], $rowpaymentdetail['id']);// get other pay amount on this Booking id on other payment number
					if (!empty($paymenttailByBooking)){
						$duevalu = $rowpaymentdetail['commision_fee']-$paymenttailByBooking['tolalpayamount'];
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
							<label id="origtotallabel'.$no.'">'.number_format($rowpaymentdetail['commision_fee'],2).'</label>
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
							<label id="duelabel'.$no.'">'.number_format($row['commision_fee_after'],2).'</label>
							<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$duevalu.'" >
							<input type="hidden" dojoType="dijit.form.TextBox" name="detailid'.$no.'" id="detailid'.$no.'" value="'.$rowpaymentdetail['id'].'" >
						</td>
						<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="'.$rowpaymentdetail['paid'].'" style="text-align: center;" ></td>
						<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$rowpaymentdetail['commision_fee_after'].'" style="text-align: center;" ></td>
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
							<label id="origtotallabel'.$no.'">'.number_format($row['commision_fee'],2).'</label>
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
						<label id="duelabel'.$no.'">'.number_format($row['commision_fee_after'],2).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$row['commision_fee_after'].'" >
						</td>
						<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
						<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['commision_fee_after'].'" style="text-align: center;" ></td>
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
	function getSumCommissionPaymentDetailByBookingId($booking_id,$detail_id){
		$db = $this->getAdapter();
		$sql="SELECT 
				SUM(pd.`paid`) AS tolalpayamount
			FROM `ldc_commission_payment_detail` AS pd,
				`ldc_commission_payment` AS p
			WHERE 
				p.id = pd.`commission_payment_id` AND
				pd.`id`!=$detail_id AND pd.`booking_id`=$booking_id 
				AND p.`status`=1
			LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getCommissionPaymentAndBookingId($payment_id,$booking_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.*,
			(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_no,
			(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_date,
			(SELECT c.commision_fee FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS commision_fee,
			(SELECT c.commision_fee_after FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS commision_fee_after
			FROM `ldc_commission_payment_detail` AS pd 
			WHERE pd.`commission_payment_id`=$payment_id AND pd.`booking_id`=$booking_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllCommissionPayment($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT 
			cp.`id`,cp.`payment_no`,
			CONCAT(a.`first_name`,' ',a.`last_name`) AS agentcy,
			cp.`payment_date`,
			(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cp.`payment_method` LIMIT 1) AS `payment_method`,
			cp.`balance`,cp.`paid`,cp.`total_due`,cp.`status`
			FROM `ldc_commission_payment` AS cp,
			`ldc_agency` AS a
			WHERE
			a.`id` = cp.`agency_id` AND
			 cp.`status`>-1 AND cp.`payment_date`>='$from_date' AND cp.`payment_date`<='$to_date'";
		$where = '';
		if($search["search_text"] !=""){
			$s_where=array();
			$s_search=addslashes(trim($search['search_text']));
			$s_where[]=" CONCAT(a.`first_name`,' ',a.`last_name`) LIKE '%{$s_search}%'";
			$s_where[]=" cp.`payment_no` LIKE '%{$s_search}%'";
			$s_where[]=" cp.`balance` LIKE '%{$s_search}%'";
			$s_where[]=" cp.`paid` LIKE '%{$s_search}%'";
			$s_where[]=" cp.`total_due` LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($search['agency_search']>0){
			$where.=" AND cp.`agency_id`=".$search['agency_search'];
		}
		$order=' ORDER BY cp.id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function addCommissionPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$reciept_no = $_db->getNewCommissionPaymentNO();
			$_arrcommission=array(
					'payment_no'	  => $reciept_no,
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
			$id_commission_payment = $this->insert($_arrcommission);
			
			$ids = explode(',', $_data['identity']);
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
			$reciept_no = $_db->getNewCommissionPaymentNO();
			$_arrcommission=array(
					'payment_no'	  => $reciept_no,
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
}
?>