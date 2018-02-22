<?php

class Bookings_Model_DbTable_DbDriverPayment extends Zend_Db_Table_Abstract  
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getDriverInfor($client_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT c.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =c.`sex` LIMIT 1) AS sexs
		FROM ldc_driver AS c WHERE id=".$client_id;
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
				$images = $baseurl."/images/driverphoto/".$row['photo'];
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
						<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['tel'].'</span>
					</li>
				</ul>
			</div>
			';
		}
		return $string;
	}
	function getCarbookingDriverIfo($data){
		$db = $this->getAdapter();
		$driver_id = $data['driver'];
		$sql=" SELECT cb.*,
			l.`location_name` AS from_location,
			tl.`location_name` AS to_location
			 FROM `ldc_carbooking` AS cb,
			 `ldc_package_location` AS l,
			`ldc_package_location` AS tl
			  WHERE 
			 l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`  
			 AND cb.`driver_id` =$driver_id  AND cb.`status`=1 AND cb.`is_paid_to_driver` =0";
		
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
							<label id="origtotallabel'.$no.'">'.number_format($row['driver_fee'],2).'</label>
						</td>
					<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
						<label id="duelabel'.$no.'">'.number_format($row['driver_fee_after'],2).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$row['driver_fee_after'].'" >
					</td>
					<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
					<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['driver_fee_after'].'" style="text-align: center;" ></td>
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
		$driver = $data['driver'];
		$sql="SELECT cb.*,
				(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`from_location` LIMIT 1) AS from_location,
				(SELECT l.`location_name` FROM ldc_package_location AS l WHERE l.id = cb.`to_location` LIMIT 1) AS to_location
				FROM `ldc_carbooking` AS cb
				WHERE cb.`status`=1 AND cb.`is_paid_to_driver`=0 AND cb.`driver_id` =$driver";
	
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
						$duevalu = $rowpaymentdetail['driver_fee']-$paymenttailByBooking['tolalpayamount'];
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
							<label id="origtotallabel'.$no.'">'.number_format($rowpaymentdetail['driver_fee'],2).'</label>
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
							<label id="duelabel'.$no.'">'.number_format($row['driver_fee_after'],2).'</label>
							<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$duevalu.'" >
							<input type="hidden" dojoType="dijit.form.TextBox" name="detailid'.$no.'" id="detailid'.$no.'" value="'.$rowpaymentdetail['id'].'" >
						</td>
						<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="'.$rowpaymentdetail['paid'].'" style="text-align: center;" ></td>
						<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$rowpaymentdetail['driver_fee_after'].'" style="text-align: center;" ></td>
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
							<label id="origtotallabel'.$no.'">'.number_format($row['driver_fee'],2).'</label>
						</td>
						<td style="vertical-align: middle; text-align: left; border-left:solid 1px #ccc;  min-width: 100px; ">&nbsp;
						<label id="duelabel'.$no.'">'.number_format($row['driver_fee_after'],2).'</label>
						<input type="hidden" dojoType="dijit.form.TextBox" name="commision_fee'.$no.'" id="commision_fee'.$no.'" value="'.$row['driver_fee_after'].'" >
						</td>
						<td><input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$no.');" name="payment_amount'.$no.'" id="payment_amount'.$no.'" value="0" style="text-align: center;" ></td>
						<td><input type="text" class="fullside" readonly="readonly" dojoType="dijit.form.NumberTextBox" required="required" name="remain'.$no.'" id="remain'.$no.'" value="'.$row['driver_fee_after'].'" style="text-align: center;" ></td>
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
	
	function getCommissionPaymentAndBookingId($payment_id,$booking_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.*,
			(SELECT c.booking_no FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_no,
			(SELECT c.booking_date FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS booking_date,
			(SELECT c.driver_fee FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS driver_fee,
			(SELECT c.driver_fee_after FROM `ldc_carbooking` AS c WHERE c.id = pd.`booking_id` LIMIT 1) AS driver_fee_after
			FROM `ldc_driver_payment_detail` AS pd 
			WHERE pd.`driver_payment_id`=$payment_id AND pd.`booking_id`=$booking_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	
	function getAllDriverPayment($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$_db = new Application_Model_DbTable_DbGlobal();
		$lang = $_db->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT 
			cp.`id`,cp.`payment_no`,
			CONCAT(a.`last_name`,'(',a.driver_id,')') AS driver_name,
			cp.`payment_date`,
			(SELECT v.".$array[$lang]." AS `name` FROM `ldc_view` AS v WHERE  v.`type`=11 AND v.`key_code`=cp.`payment_method` LIMIT 1) AS `payment_method`,
			cp.`balance`,cp.`paid`,cp.`total_due`,cp.`status`
			FROM `ldc_driver_payment` AS cp,
			`ldc_driver` AS a
			WHERE
			a.`id` = cp.`driver_id` AND
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
		if (!empty($search['driver_search'])){
			$where.=" AND cp.`driver_id`=".$search['driver_search'];
		}
		//print_r($sql.$where);
		$order=' ORDER BY cp.id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function addCommissionPayment($_data){
		 
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$reciept_no = $_db->getNewDriverPaymentNO();
			$_arrcommission=array(
					'payment_no'	=> $reciept_no,
					'driver_id'	  	=> $_data['driver'],
					'payment_date'	=> $_data['payment_date'],
					'payment_type'	=> 0,
					'payment_method'=> $_data['payment_method'],
					'paid'	  		=> $_data['total_paid'],
					'balance'	  	=> $_data['balance'],
					'total_due'	  	=> $_data['total_due'],
					'amount'      	=> $_data['amount'],
					'note'	  		=> $_data['remark'],
					'status'	  	=> 1,
					'create_date'	=> date("Y-m-d H:i:s"),
					'modify_date'  	=>date("Y-m-d H:i:s"),
					'user_id'      	=> $this->getUserId(),
			);
			$this->_name="ldc_driver_payment";
			$id_driver_payment = $this->insert($_arrcommission);
			
			$ids = explode(',', $_data['identity']);
			$dueafter=0;
			foreach ($ids as $i){
				$is_payment =0;
				$booking = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid = $_data['payment_amount'.$i];
				if (!empty($booking)){
					$dueafter =$booking['driver_fee_after']-$paid;
					if ($dueafter>0){
						$is_payment=0;
					}else{
						$is_payment=1;
					}
					$array=array(
							'is_paid_to_driver'=>$is_payment,
							'driver_fee_after' =>$dueafter,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				$arrs = array(
						'driver_payment_id'	   =>$id_driver_payment,
						'booking_id'           =>$_data['carbooking_id'.$i],
						'due_amount'           =>$_data['commision_fee'.$i],
						'paid'                 =>$_data['payment_amount'.$i],
						'remain'               =>$_data['remain'.$i],
				);
				$this->_name ='ldc_driver_payment_detail';
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
			$rows=$this->getDriverPaymentDetail($_data['driver_payment_id']);
			if(!empty($rows)) foreach ($rows As $result){
				$row_paid=$this->getSumDriverPayment($result['booking_id'],$result['driver_payment_id']);
				$driver_fee_after=$this->getCarbookingById($result['booking_id']);
				$arr_car=array(
						'driver_fee_after'=> ($row_paid['tolalpayamount'])+($driver_fee_after['driver_fee_after']),
						);
				$this->_name="ldc_carbooking";
				$where=" id=".$driver_fee_after['id'];
				$this->update($arr_car, $where);
			}
			
			$_db = new Application_Model_DbTable_DbGlobal();
			$reciept_no = $_db->getNewDriverPaymentNO();
			$_arrcommission=array(
					'payment_no'	=> $_data['reciept_no'],
					'driver_id'	  	=> $_data['driver'],
					'payment_date'	=> $_data['payment_date'],
					'payment_type'	=> 0,
					'payment_method'=> $_data['payment_method'],
					'paid'	  		=> $_data['total_paid'],
					'balance'	  	=> $_data['balance'],
					'total_due'	  	=> $_data['total_due'],
					'amount'      	=> $_data['amount'],
					'note'	  		=> $_data['remark'],
					'status'	  	=> 1,
					'create_date'	=> date("Y-m-d H:i:s"),
					'modify_date'  	=>date("Y-m-d H:i:s"),
					'user_id'      	=> $this->getUserId(),
			);
			$this->_name="ldc_driver_payment";
			$where=" id=".$_data['driver_payment_id'];
			$this->update($_arrcommission, $where);
			
			$sql =" DELETE FROM ldc_driver_payment_detail WHERE driver_payment_id=".$_data['driver_payment_id'];
			$db->query($sql);
				
			$ids = explode(',', $_data['identity']);
			$dueafter=0;
			foreach ($ids as $i){
				$is_payment =0;
				$booking = $this->getCarbookingById($_data['carbooking_id'.$i]);
				$paid = $_data['payment_amount'.$i];
				if (!empty($booking)){
					$dueafter =$booking['driver_fee_after']-$paid;
					if ($dueafter>0){
						$is_payment=0;
					}else{
						$is_payment=1;
					}
					$array=array(
							'is_paid_to_driver'=>$is_payment,
							'driver_fee_after' =>$dueafter,
					);
					$this->_name="ldc_carbooking";
					$where = " id =".$_data['carbooking_id'.$i];
					$this->update($array, $where);
				}
				$arrs = array(
						'driver_payment_id'	   =>$_data['driver_payment_id'],
						'booking_id'           =>$_data['carbooking_id'.$i],
						'due_amount'           =>$_data['commision_fee'.$i],
						'paid'                 =>$_data['payment_amount'.$i],
						'remain'               =>$_data['remain'.$i],
				);
				$this->_name ='ldc_driver_payment_detail';
				$this->insert($arrs);
			}
				
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	function getCommissionPaymentByID($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `ldc_driver_payment` AS c WHERE c.`id` =$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	
	function getCommissionPaymentDetail($driver_payment_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.* FROM `ldc_driver_payment_detail` AS pd WHERE pd.`driver_payment_id`=$driver_payment_id";
		return $db->fetchAll($sql);
	}
	
	function getSumDriverPayment($booking_id,$driver_payment_id){
		$db = $this->getAdapter();
		$sql=" SELECT
		SUM(pd.`paid`) AS tolalpayamount
		FROM `ldc_driver_payment` AS p,`ldc_driver_payment_detail` AS pd
		WHERE   p.id = pd.`driver_payment_id` AND
		pd.`driver_payment_id`=$driver_payment_id AND pd.`booking_id`=$booking_id
		AND p.`status`=1 LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	function getDriverPaymentDetail($driver_payment_id){
		$db = $this->getAdapter();
		$sql="SELECT pd.* FROM `ldc_driver_payment_detail` AS pd WHERE pd.`driver_payment_id`=$driver_payment_id";
		return $db->fetchAll($sql);
    }
    
    function getCarbookingById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT c.* FROM `ldc_carbooking` AS c WHERE c.`id` = $id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    
    function getSumCommissionPaymentDetailByBookingId($booking_id,$driver_payment_id){
    	$db = $this->getAdapter();
    	$sql=" SELECT
    	SUM(pd.`paid`) AS tolalpayamount
    	FROM `ldc_driver_payment` AS p,`ldc_driver_payment_detail` AS pd
    	WHERE   p.id = pd.`driver_payment_id` AND
    	pd.`driver_payment_id`=$driver_payment_id AND pd.`booking_id`=$booking_id
    	AND p.`status`=1 LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
}
?>