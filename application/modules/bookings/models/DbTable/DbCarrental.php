<?php

class Bookings_Model_DbTable_DbCarrental extends Zend_Db_Table_Abstract
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
	function getVehicleInfo($data){
		$vehilce_id = empty($data['vehicle'])?0:$data['vehicle'];
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
			AND v.`status`=1 AND v.`id` ='.$vehilce_id.' LIMIT 1';
		$row = $db->fetchRow($sql);
		$string='';
		$defaultgst = 0;
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$image = $baseurl."/images/no_car.png";
		if(!empty($row)){
			if (!empty($row['img_front'])){
				$image = $baseurl."/images/vehicle/".$row['img_front'];
			}
			$string.='
			<td style="text-align: center;vertical-align: middle; ">'.$data['record_no'].'</td>
			<td style="vertical-align: middle; text-align: center; border-left:solid 1px #ccc;"> 
				<img class="image_view" id="image_view1" src="'.$image.'" alt="" style="width: 70px;max-height: 40px;">
			</td>
			<td style="vertical-align: middle; text-align: left;  ">&nbsp;<label id="labelservice'.$data['record_no'].'">'.$row['name'].'<label>
				<input type="hidden" dojoType="dijit.form.TextBox" name="vehicle_id'.$data['record_no'].'" id="vehicle_id'.$data['record_no'].'" value="'.$row['id'].'" >
			</td>
			<td>
				<input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$data['record_no'].');"  name="rent_price'.$data['record_no'].'" id="rent_price'.$data['record_no'].'" value="0" style="text-align: center;" >
				<input type="hidden" dojoType="dijit.form.TextBox" name="make'.$data['record_no'].'" id="make'.$data['record_no'].'" value="'.$row['make'].'" >
				<input type="hidden" dojoType="dijit.form.TextBox" name="model'.$data['record_no'].'" id="model'.$data['record_no'].'" value="'.$row['model'].'" >
				<input type="hidden" dojoType="dijit.form.TextBox" name="submodel'.$data['record_no'].'" id="submodel'.$data['record_no'].'" value="'.$row['submodel'].'" >
			</td>
			<td>
				<input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required" onKeyup="calculateamount('.$data['record_no'].');"  name="other_fee'.$data['record_no'].'" id="other_fee'.$data['record_no'].'" value="0" style="text-align: center;" >
			</td>
			<td>
				<input type="text" class="fullside" dojoType="dijit.form.NumberTextBox" required="required"  readonly="readonly" name="amount'.$data['record_no'].'" id="amount'.$data['record_no'].'" value="0" style="text-align: center;" >
			</td>
			<td style="cursor: pointer;text-align: center;  vertical-align: middle;">
				<img onclick="newdeleteRecord('.$data['record_no'].')" src="'.$baseurl.'/images/Delete_16.png">
				<input type="hidden" dojoType="dijit.form.TextBox" name="detailid'.$data['record_no'].'" id="detailid'.$data['record_no'].'" value="" >
			</td>
			';
		}
		$array = array('stringrow'=>$string,'rsult'=>$row,'newrowid'=>$data['record_no']);
		return $array;
	}
	function getAllCarBooking($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$sql="
		SELECT cr.id,cr.`rent_no`,
			CONCAT(c.`first_name`,' ',c.`last_name`) AS `name`,
			cr.`rent_date`,cr.`start_date`,cr.`return_date`,cr.`return_time`,
			cr.`total_payment`,cr.`total_rent_fee`,cr.`refundable_deposit`,cr.`paid`,cr.`balance`,cr.`status`
		FROM 
			`ldc_carrental` AS cr,
			`ldc_customer` AS c
		WHERE 
			c.id = cr.`customer_id`
			AND cr.`status`>-1 AND cr.`rent_date`>='$from_date' AND cr.`rent_date`<='$to_date' ";
		$where = '';
// 		if($search["search_text"] !=""){
// 			$s_where=array();
// 			$s_search=addslashes(trim($search['search_text']));
// 			$s_where[]=" CONCAT(c.`first_name`,' ',c.`last_name`) LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`rent_no` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`total_payment` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`total_rent_fee` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`refundable_deposit` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`paid` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`balance` LIKE '%{$s_search}%'";
// 			$s_where[]=" cr.`return_time` LIKE '%{$s_search}%'";
// 			$where.=' AND ('.implode(' OR ',$s_where).')';
// 		}
// 		if ($search['customer']>0){
// 			$where.=" AND cr.`customer_id`=".$search['customer'];
// 		}
		$order=' ORDER BY cr.id DESC';
		return $db->fetchAll($sql.$where.$order);
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
	
	function checkedCustomer($id){
	    $db = $this->getAdapter();
	    $sql=" SELECT id FROM `ldc_carrental_customer` WHERE id=$id AND `status`=1 AND `customer`!='' LIMIT 1";
	    return $db->fetchOne($sql);
	}
	
	public function addCarrental($_data){
	   // print_r($_data);exit();
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
		if(!empty($_data['customer'])){
		    $cus=$this->checkedCustomer($_data['customer']);
		        $cus_data=array(
		            'phone'	      => $_data['phone'],
		            'passport'	  => $_data['passport'],
		            'address'	  => $_data['address'],
		            'user_id'     => $this->getUserId(),
		            'status'      => 1,
		        );
		        $this->_name="ldc_carrental_customer";
		        if(!empty($cus)){
		            $cus_data['modify_date']=date("Y-m-d H:i:s");
		            $where=" id=".$cus;
		            $cus_id=$cus;
		            $this->update($cus_data, $where);
		        }else{
		            $cus_data['create_date']=date("Y-m-d H:i:s");
		            $cus_id=$this->insert($cus_data);
		        }
		   }
		   
		   if(!empty($_data['vehicle_type'])){
		       $veh_data=array(
		           'reffer'	      => $_data['vehicle_ref_no'],
		           'color'	      => $_data['color'],
		           'modify_date'  => date("Y-m-d H:i:s"),
		           'user_currental'=> $this->getUserId(),
		           'status'        => 1,
		       );
		       $this->_name="ldc_vehicle";
		       $where=" car_type=".$_data['vehicle_type'];
		       $this->update($veh_data, $where);
		   }
		   
			$_db = new Application_Model_DbTable_DbGlobal();
			$rent_code = $_db->getCarrentalNO();
			$_arrbooking=array(
			        'customer_id'  => $cus_id,
			        'rent_no'	   => $rent_code,
			        'rent_date'	   => date("Y-m-d H:i:s",strtotime($_data['rent_date'])),
			        'start_date'   => date("Y-m-d H:i:s",strtotime($_data['validity_date'])),
			        'return_date'  => date("Y-m-d H:i:s",strtotime($_data['return_date'])),
					 
					'vehicle_type' => $_data['vehicle_type'],
					'color'	       => $_data['color'],
					'deposit'	   => $_data['deposit'],
					'return_money' => $_data['return_money'],
					'cost_month'   => $_data['cost_month'],
			    
			        'total_rent_num'=> $_data['total_rent_fee'],
			        'total_maintenance'=> $_data['total_maintenance'],
			        'total_payment'	=> $_data['total_payment'],
			        'profit'	    => $_data['profit'],
			    
			        'is_return_car'	=> 0,
					'create_date'   => date("Y-m-d H:i:s"),
					'modify_date'   =>date("Y-m-d H:i:s"),
					'user_id'       => $this->getUserId(),
			        'status'        => 1,
					
			);
			$this->_name="ldc_carrental";
			$idcarrental = $this->insert($_arrbooking);
			
			$_car_detail=array(
			    'carrental_id'  => $idcarrental,
			    'time'	        => $_data['time'],
			    'fix_name'	    => $_data['fix_name'],
			    'repair_date'	=> $_data['repair_date'],
			    'payment_date'	=> $_data['payment_date'],
			    'toatal_amount_fix'=> $_data['toatal_amount_fix'],
			    'paid'	        => $_data['paid'],
			    'remark'	    => $_data['first-name'],
			    'create_date'   => date("Y-m-d H:i:s"),
			    'user_id'       => $this->getUserId(),
			    'status'        => 1,
			    
			);
			$this->_name="ldc_carrental_detail";
			$carr_detail_id = $this->insert($_car_detail);
			
// 			$row=$this->getAllPaidMonth($id);
			
			$part= PUBLIC_PATH.'/images/imgbong/';
			if (!file_exists($part)) {
			    mkdir($part, 0777, true);
			}
			$photoname = str_replace(" ", "_", $_data['fix_name']);
			$ids = explode(',', $_data['record_row']);
			$image_name="";
			if(!empty($_data['record_row'])){
			    foreach ($ids as $i){
			        if (!empty($_FILES['photo'.$i]['name'])){
			            $ss = 	explode(".", $_FILES['photo'.$i]['name']);
			            $new_image_name = $photoname.$i.".".end($ss);
			            $tmp = $_FILES['photo'.$i]['tmp_name'];
			            if(move_uploaded_file($tmp, $part.$new_image_name)){
			                $image_name = $new_image_name;
			            }
			        }else{
			            $image_name ="";
			        }
			        
			        $arr = array(
			            'carr_detail_id' =>$carr_detail_id,
			            'pic_title'      =>$image_name,
			            'status'         =>$_data['status'],
			            'date'           =>date("Y-m-d")
			        );
			        $this->_name='ldc_carrental_img';
			        $this->insert($arr);
			    }
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
	
	function getAllPaidMonth($id){
	    $db = $this->getAdapter();
	    $sql=" SELECT cd.`toatal_amount_fix`,cd.`paid` 
               FROM `ldc_carrental` AS c,`ldc_carrental_detail` AS cd
               WHERE c.`id`=cd.`carrental_id`
               AND cd.`carrental_id`=$id";
	    return $db->fetchRow($sql);
	}
	
	function getAllCustomer(){
	    $db = $this->getAdapter();
	    $sql=" SELECT c.`customer` AS `name`,c.* FROM `ldc_carrental_customer` AS c WHERE c.`status`=1 ";
	    return $db->fetchAll($sql);
	}
	
	function getAllCustomerById($id){
	    $db = $this->getAdapter();
	    $sql=" SELECT c.`customer` AS `name`,c.*,
        (SELECT v.name_en FROM `tb_view` AS v WHERE v.key_code=c.`sex` AND v.type=13 LIMIT 1)AS gender 
        FROM `ldc_carrental_customer` AS c WHERE c.`status`=1 AND id=".$id;
	    return $db->fetchRow($sql);
	}
	
	function getVehcleInfor($vehicle_id){
	    $db = $this->getAdapter();
	    $sql="SELECT v.`car_type`,v.reffer as car_no,v.`color`,
	    (SELECT veh.title FROM `ldc_vechicletye` AS veh WHERE veh.id=v.`car_type`  LIMIT 1) AS vehicle_type
	    FROM `ldc_vehicle` AS v
	    WHERE v.`status`=1
	    AND v.`car_type`=$vehicle_id";
	    return $db->fetchRow($sql);
	}
	
}
?>