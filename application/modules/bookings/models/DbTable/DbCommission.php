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
		$sql=" SELECT cb.* FROM `ldc_carbooking` AS cb WHERE cb.`agency_id` =$agent  AND cb.`status`=1 AND cb.`is_paid_commission` =0";
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
				<tr id="row'.$no.'" style="background: #fff; border: solid 1px #bac;">
					<td align="center" style="  padding: 0 10px;"><input  OnChange="CheckAllTotal('.$no.')" style=" vertical-align: top; height: initial;" type="checkbox" class="checkbox" id="mfdid_'.$no.'" value="'.$no.'"  name="selector[]"/></td>
					<td style="text-align: center;vertical-align: middle; ">'.($key+1).'</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
						<label id="billingdatelabel'.$no.'">'.date("d-M-Y",strtotime($row['booking_date'])).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="carbooking_id'.$no.'" id="carbooking_id'.$no.'" value="'.$row['id'].'" >
					</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc; min-width: 100px;">&nbsp;
						<label id="invoicelabel'.$no.'">'.$row['booking_no'].'</label>
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
	function getAllCarBooking($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$sql="
		SELECT cb.id,cb.`booking_no`,
			CONCAT(c.`first_name`,' ',c.`last_name`) AS cus_name,
			l.`location_name` AS from_location,
			tl.`location_name` AS to_location,
			cb.`booking_date`,
			cb.`delivey_date`,
			cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
			(SELECT CONCAT(d.`first_name`,' ',d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,
			cb.`status`
			FROM `ldc_carbooking` AS cb,
			`ldc_customer` AS c,
			`ldc_package_location` AS l,
			`ldc_package_location` AS tl
			WHERE 
			c.`id` = cb.`customer_id` 
			AND l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`
			AND cb.`status` >-1 AND cb.`booking_date`>='$from_date' AND cb.`booking_date`<='$to_date'";
		$where = '';
		if($search["search_text"] !=""){
			$s_where=array();
			$s_search=addslashes(trim($search['search_text']));
			$s_where[]=" CONCAT(c.`first_name`,' ',c.`last_name`) LIKE '%{$s_search}%'";
			$s_where[]=" cb.`booking_no` LIKE '%{$s_search}%'";
			$s_where[]=" tl.`location_name` LIKE '%{$s_search}%'";
			$s_where[]=" l.`location_name` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`price` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`commision_fee` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`other_fee` LIKE '%{$s_search}%'";
			$s_where[]=" cb.`total` LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($search['customer']>0){
			$where.=" AND cb.`customer_id`=".$search['customer'];
		}
		$order=' ORDER BY cb.id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function addCommissionPayment($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$booking_code = $_db->getNewCarBookingNO();
			$_arrcommission=array(
					'payment_no'	  => $_data['customer'],
					'agency_id'	  => $_data['driver'],
					'payment_date'	  => $_data['vehicle'],
					'payment_type'	  => $_data['agency'],
					'payment_method'	  => $booking_code,
					'paid'	  => $_data['booking_date'],
					'balance'	  => $_data['delivery_date'],
					'grand_total'	  => $_data['from_location'],
					'note'	  => $_data['to_location'],
					'status'	  => 1,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_commission_payment";
			$idbooking = $this->insert($_arrcommission);
			
			if ($_data['total_paid']>0){
				$_data['booking_id'] = $idbooking;
				$this->addCarbookingPayment($_data);
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
	function addCarbookingPayment($_data){
// 		$db = $this->getAdapter();
// 		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$payment_no = $_db->getNewPaymentNO();
			$_arrpayment=array(
					'payment_no'	  => $payment_no,
					'customer_id'	  => $_data['customer'],
					'payment_date'	  => $_data['booking_date'],
					'payment_type'	  => $_data['agency'],
					'payment_method'	  => $_data['payment_method'],
					'paid'	  => $_data['total_paid'],
					'balance'	  => $_data['balance'],
					'grand_total'	  => $_data['total'],
					'note'	  => $_data['payment_note'],
					'status'	  => 1,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_carbooking_payment";
			$idpayment = $this->insert($_arrpayment);
			
			$_arrpaymentdetail=array(
					'payment_id'	  => $idpayment,
					'booking_id'	  => $_data['booking_id'],
					'due_amount'	  => $_data['total'],
					'paid'	  => $_data['total_paid'],
					'remain'	  => $_data['balance'],
					'paid_from'	  => 1,// paid from booking form
			);
			$this->_name="ldc_carbooking_payment_detail";
			$idpayment = $this->insert($_arrpaymentdetail);
			
			$dueafter=0;
			$is_payment =0;
			$bookings = $this->getCarbookingById($_data['booking_id']);
			$paid = $_data['total_paid'];
			if (!empty($bookings)){
				$dueafter =	$bookings['due_after']-$paid;
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
				$where = " id=".$_data['booking_id'];
				$this->update($array, $where);
			}
// 			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
// 			$db->rollBack();
		}
	}
}
?>