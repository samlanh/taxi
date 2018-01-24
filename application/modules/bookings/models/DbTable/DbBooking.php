<?php

class Bookings_Model_DbTable_DbBooking extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getCustomerInfor($client_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT c.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =c.`sex` LIMIT 1) AS sexs
		FROM ldc_customer AS c WHERE id=".$client_id;
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
		
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$string="";
		if (!empty($row)){
			if (!empty($row['photo'])){
				$images = $baseurl."/images/profile/".$row['photo'];
			}else{
				$images = $baseurl."/images/profile.jpg";
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
		}else{
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
		}
		 return $string;
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
	
	function getDriverAndCarInfor($data){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT d.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =d.`sex` LIMIT 1) AS sexs
		 FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!=''
		";
		if ($data['type']==1){
			$sql.=" AND d.id=".$data['id'];
		}else if ($data['type']==2){
			$sql.=" AND d.vehicle_id=".$data['id'];
		}
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
	
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$vehicleid = 0;
		$driver_id = 0;
		
		$images_car = $baseurl."/images/no_car.png";
		$images = $baseurl."/images/profile.jpg";
		
		$vehicle_string='
		<h4 class="car_title"></h4>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
				<img id="profile_wiew" src="'.$images_car.'" alt=""  />
			</div>
		</div>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
				<li>
					<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value"></span>
				</li>
			</ul>
		</div>
		';
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
			$vehicleid = $row['vehicle_id'];
			$driver_id = $row['id'];
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
			$vehicle =	$this->getvehicleinfo($row['vehicle_id']);
			if (!empty($vehicle)){
				if (!empty($vehicle['img_front'])){
					$images_car = $baseurl."/images/vehicle/".$vehicle['img_front'];
				}
				$vehicle_string='
					<h4 class="car_title">'.$vehicle['make'].' '.$vehicle['model'].' '.$vehicle['submodel'].'</h4>
		       				<div class="col-md-4 col-sm-4 col-xs-12">
				               	<div class="image-box infor">
									<img id="profile_wiew" src="'.$images_car.'" alt=""  />
								</div>
							</div>
							<div class="col-md-8 col-sm-8 col-xs-12">
								<ul class="list-unstyled">
                              		<li>
                              			<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value">'.$vehicle['reffer'].'</span>
                              		</li>
                              		<li>
                              			<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value">'.$vehicle['year'].'</span>
                              		</li>
                              		<li>
                              			<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value">'.$vehicle['color'].'</span>
                              		</li>
                              	</ul>
							</div>
					';
			}
		}
		$array = array(
				'driver'=>$string,
				'vehicle'=>$vehicle_string,
				'driver_id'=>$driver_id,
				'vehicle_id'=>$vehicleid,
				);
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
	function getvehicleinfo($vehilce_id){ //add & edit driver
		$db = $this->getAdapter();
		$sql='
		SELECT v.*,
		m.`title` AS make,
		mo.`title` AS model,
		smo.`title` AS submodel,
		CONCAT(m.`title`," ",mo.`title`," ",smo.`title`," (",v.`reffer`,")") AS `name`,
		(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
		(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
		(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine`
		FROM `ldc_vehicle` AS v,
		`ldc_make` AS m,
		`ldc_model` AS mo,
		`ldc_submodel` AS smo
		WHERE
		v.`make_id` = m.`id` AND
		v.`model_id` = mo.`id` AND
		v.`sub_model` = smo.`id` AND
		v.is_sale !=1
		AND v.`status`=1 AND v.`id` ='.$vehilce_id.' LIMIT 1
		';
		return $row = $db->fetchRow($sql);
	}
	function getDriverInformation($driver_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT d.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =d.`sex` LIMIT 1) AS sexs
		FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!=''
		AND d.id =$driver_id LIMIT 1
		";
		return $db->fetchRow($sql);
	}
	public function addCarBooking($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$booking_code = $_db->getNewCarBookingNO();
			$_arrbooking=array(
					'customer_id'	  => $_data['customer'],
					'driver_id'	  => $_data['driver'],
					'vehicle_id'	  => $_data['vehicle'],
					'agency_id'	  => $_data['agency'],
					'booking_no'	  => $booking_code,
					'booking_date'	  => $_data['booking_date'],
					'delivey_date'	  => $_data['delivery_date'],
					'delivey_time'	  => $_data['delivery_time'],
					'fly_no'	  => $_data['fly_no'],
					'from_location'	  => $_data['from_location'],
					'to_location'	  => $_data['to_location'],
					'qty'	  => 1,
					'price'	  => $_data['price'],
					'commision_fee'	  => $_data['commision_fee'],
					'commision_fee_after'	  => $_data['commision_fee'],
					'is_paid_commission'	  => 0,
					'other_fee'	  => $_data['other_fee'],
					'total'	  => $_data['total'],
					'due'	  => $_data['total'],
					'due_after'	  => $_data['total'],
					'driver_fee'	  => $_data['driver_fee'],
					'driver_fee_after'	  => $_data['driver_fee'],
					'remark'	  => $_data['remark'],
					'status'	  => 1,
					'is_paid_to_driver'	  => 0,
					'is_customer_paid'	  => 0,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_carbooking";
			$idbooking = $this->insert($_arrbooking);
			
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
	public function updateCarBooking($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$commission_pay = $this->getTotalCommissionFee($_data['booking_id']);
			$commiss_paid = 0;
			if (!empty($commission_pay)){
				$commiss_paid = $commission_pay['total_paid_commiss'];
			}
			$commision_fee_after = $_data['commision_fee'] - $commiss_paid;
			$is_commissionpaid = 0;
			if ($commision_fee_after<=0){
				$is_commissionpaid =1;
			}
			
			$driverfee_pay = $this->getTotalDriverFee($_data['booking_id']);
			$driverfee_paid = 0;
			if (!empty($driverfee_pay)){
				$driverfee_paid = $driverfee_paid['total_driver_fee'];
			}
			$driver_feeafter = $_data['driver_fee']-$driverfee_paid;
			$is_driverpaid = 0;
			if ($driver_feeafter<=0){
				if ($_data['driver']>0){
					$is_driverpaid =1;
				}
			}
			$_arrbooking=array(
					'customer_id'	  => $_data['customer'],
					'driver_id'	  => $_data['driver'],
					'vehicle_id'	  => $_data['vehicle'],
					'agency_id'	  => $_data['agency'],
// 					'booking_no'	  => $booking_code,
					'booking_date'	  => $_data['booking_date'],
					'delivey_date'	  => $_data['delivery_date'],
					'delivey_time'	  => $_data['delivery_time'],
					'fly_no'	  => $_data['fly_no'],
					'from_location'	  => $_data['from_location'],
					'to_location'	  => $_data['to_location'],
					'qty'	  => 1,
					'price'	  => $_data['price'],
					'commision_fee'	  => $_data['commision_fee'],
					'commision_fee_after'	  => $commision_fee_after,
					'is_paid_commission'	  => $is_commissionpaid,
					'other_fee'	  => $_data['other_fee'],
					'total'	  => $_data['total'],
					'due'	  => $_data['total'],
					'due_after'	  => $_data['total'],
					'driver_fee'	  => $_data['driver_fee'],
					'driver_fee_after'	  => $driver_feeafter,
					'remark'	  => $_data['remark'],
					'status'	  => 1,
					'is_paid_to_driver'	  => $is_driverpaid,
					'is_customer_paid'	  => 0,
					'create_date'=> date("Y-m-d H:i:s"),
					'modify_date'  =>date("Y-m-d H:i:s"),
					'user_id'      => $this->getUserId(),
			);
			$this->_name="ldc_carbooking";
			$where = " id = ".$_data['booking_id'];
			$this->update($_arrbooking, $where);
			$idbooking = $_data['booking_id'];

			$chekcpayment = $this->checkBookingHasPayment($idbooking);
			if (empty($chekcpayment)){
				if ($_data['total_paid']>0){
					$_data['booking_id'] = $idbooking;
					$this->addCarbookingPayment($_data);
				}
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getTotalCommissionFee($booking_id){
		$db = $this->getAdapter();
		$sql="
			SELECT 
				SUM(pd.`paid`) AS total_paid_commiss
			FROM `ldc_commission_payment_detail` AS pd,
				`ldc_commission_payment` AS p
			WHERE 
				p.id = pd.`commission_payment_id` AND pd.`booking_id` =$booking_id
				AND p.`status` =1";
		return $db->fetchRow($sql);
	}
	function getTotalDriverFee($booking_id){
		$db = $this->getAdapter();
		$sql="
		SELECT 
			SUM(pd.`paid`) AS total_driver_fee
		FROM `ldc_driver_fee_payment_detail` AS pd,
			`ldc_driver_fee_payment` AS p
		WHERE 
			p.id = pd.`driverfee_payment_id` AND pd.`booking_id` =$booking_id
			AND p.`status` =1";
		return $db->fetchRow($sql);
	}
	function checkBookingHasPayment($booking_id){
		$db = $this->getAdapter();
		$sql="
			SELECT cpd.* 
			FROM `ldc_carbooking_payment_detail` AS cpd,
			`ldc_carbooking_payment` AS cp
			WHERE 
				cp.`id` = cpd.`payment_id`
				AND cpd.`booking_id` = $booking_id
				AND cp.`status`=1";
		return $db->fetchRow($sql);
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