<?php

class Bookings_Model_DbTable_DbCarRentalNew extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getOwnerById($id){
		$db = $this->getAdapter();
		$sql = "SELECT id,owner_name,`position`,id_card,hand_phone,email,hotline,`status` FROM ldc_owner where id= ".$id;
		return $db->fetchRow($sql);
	}
	
	public function addBookingRental($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$db_globle = new Application_Model_DbTable_DbGlobal();
    		$booking_code = $db_globle->getNewBookingCode();
    		
    		
    		$diff=date_diff(date_create($data["pickup_date"]),date_create($data["return_date"]));
    		$total_day = $diff->format("%a%")+1;
    	
    		
    		$rows = $this->getProductSelected($data);
    		$row_driver = $this->getDriverSelected($data);
    		
    		$pickup_location = $this->getLocationById($data['pickup_location']);
    		$return_location = $this->getLocationById($data['return_location']);
    		$row_pickup =$this->getPickUpPriceMultiVehicle($data);
    		
    		
    		//Vehicle Blog
    		$total_deposit_vehicle=0;
    		$total_price_vehicle=0;
    		$vat_vehicle = 0;
    		if(!empty($data['identity_vehicle'])){
    			$ids = explode(',', $data['identity_vehicle']);
    			if(!empty($ids))foreach ($ids as $p){
    				$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
    				$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
    				$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
    		
    				$total_row_price_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
    				
    				$total_price_vehicle = number_format(($total_price_vehicle + $total_row_price_vehicle),2);
    				$vat_vehicle = $vat_vehicle + $row_vehicle_price["vat_value"];
    			}
    		}
    		$total_deposit_vehicle = $data['refundable_deposit'];
    		$vehicle_otherFee = ($total_deposit_vehicle+$data['long_dast'])-$data['discount_value'];
    		
    		//Product blog
    		$total_price_product =0;
    		if(!empty($rows)){
    			foreach ($rows as $row){
    				$total_price_product+= number_format(($row['pro_price']*$row['amount_rent'])*$total_day,2);
    			}
    		}
    		
    		// Driver Blog
    		$total_price_driver = 0;
    		if(!empty($row_driver)){
    			foreach ($row_driver as $row){
    				$total_price_driver+= number_format(($row['driver_price']*$row['amount_rent'])*$total_day,2);
    			}
    		}
    		// other fee vehicle
	    		$Extra_ch_sun = 0;
				if($data["sunday_price"]>0){
					$Extra_ch_sun = $data["sunday_price"];
				}
				$airport_price=0;
				if($data["airport_price"]>0){
					$airport_price = $data["airport_price"];
				}
				$dropairport_price=0;
				if($data["dropairport_price"]>0){
					$dropairport_price = $data["dropairport_price"];
				}
				$item_1=0;
				if($data["item_1"]>0){
					$item_1 = $data["item_1"];
				}
				$item_2=0;
				if($data["item_2"]>0){
					$item_2 = $data["item_2"];
				}
				$item_3=0;
				if($data["item_3"]>0){
					$item_3 = $data["item_3"];
				}
				$total_otherfeevehicel = $Extra_ch_sun+$airport_price+$dropairport_price+$item_1+$item_2+$item_3;
			
    		//Pickup & Return Blog
	    		$total_price_pickup = round($row_pickup["price"]+($row_pickup["price"]*$row_pickup["tax"]/100),2);
	    		$vat_pickup = $row_pickup["tax"];
    		///
    		$total_payment = $total_price_vehicle+$total_price_driver+$total_price_product+$total_price_pickup+$total_otherfeevehicel+$vehicle_otherFee;
    		$diposit = round(($total_payment*50/100)+(($total_payment*50/100)*3/100),2);
    		
    		
    		if($data["payment_type"]==4){
    			$total_fee = $total_payment;
    			$deposit_fee = 0;
    			$total_pay = $data["cash_pay"];
    		}else{
    			$total_fee = $total_payment;
    			$deposit_fee = $diposit;
    			$total_pay = $diposit;
    		}
    		
			$arr = array(
					'customer_id'			=>	$data["customer"],
					'booking_no'			=>	$booking_code,
					'date_book'			=>	date("Y-m-d"),
					'pickup_date'			=>	date("Y-m-d",strtotime($data["pickup_date"])),
					'pickup_time'			=>	$data['pickup_time'].":".$data['pickup_minute'],
					'return_date'			=>	date("Y-m-d",strtotime($data["return_date"])),
					'return_time'			=>	$data['return_time'].":".$data['return_minute'],
					'total_fee'				=>	$total_fee,
					'total_paymented'		=>	$total_pay,
					'item_type'				=>	1,
					'pickup_location'		=>	$pickup_location["id"],
					'dropoff_location'		=>	$return_location["id"],
					'fly_no'				=>	$data["fly_no"],
					'fly_date'				=>	date("Y-m-d",strtotime($data["fly_date"])),
					'fly_time_of_arrival'	=>	$data["fly_time"],
					'fly_destination'		=>	$data["fly_destination"],
					'status'				=>	1,
					'deposite_fee'			=>	$deposit_fee,
					'total_vat'				=>	$vat_pickup+$vat_vehicle,
					'user_id'				=>	$this->getUserId(),
			);
			
			if($data["payment_type"]==1){
				$arr['visa_name']= $data["card_name"];
				$arr['card_id']  = $data["card_id"];
				$arr['secu_code']= $data["secu_code"];
				$arr['card_exp_date']=$data["card_exp_date"];
				$arr['card_id']  = $data["card_id"];
				$arr['payment_type']=1;
			
			}elseif($data["payment_type"]==2){
				$arr['card_id']=$data["wu_code"];
				$arr['payment_type']=2;
			}elseif($data["payment_type"]==3){
				$arr['payment_type']=3;
			}elseif($data["payment_type"]==4){
				$arr['payment_type']=4;
			}
			
			$this->_name = "ldc_booking";
			$book_id = $this->insert($arr);
			$data['booking_id'] = $book_id;
			
			$this->addAgreement($data); 
			//Other fee vehicle
			if($data["sunday_price"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	"Extra Charge Sunday | ".$data["sunday_price_remake"],
						'price'				=>	$data["sunday_price"],
						'total'				=> 	$data["sunday_price"],
						'total_paymented'	=>	$data["sunday_price"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			if($data["airport_price"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	"Pickup Airport | ".$data["airport_price_remake"],
						'price'				=>	$data["airport_price"],
						'total'				=> 	$data["airport_price"],
						'total_paymented'	=>	$data["airport_price"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			if($data["dropairport_price"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	"Drop of Airport | ".$data["dropairport_price_remake"],
						'price'				=>	$data["dropairport_price"],
						'total'				=> 	$data["dropairport_price"],
						'total_paymented'	=>	$data["dropairport_price"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			if($data["item_1"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	$data["item_1_remake"],
						'price'				=>	$data["item_1"],
						'total'				=> 	$data["item_1"],
						'total_paymented'	=>	$data["item_1"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			if($data["item_2"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	$data["item_2_remake"],
						'price'				=>	$data["item_2"],
						'total'				=> 	$data["item_2"],
						'total_paymented'	=>	$data["item_2"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			if($data["item_3"]>0){
				$arr = array(
						'book_id'			=>	$book_id,
						'item_name'			=>	$data["item_3_remake"],
						'price'				=>	$data["item_3"],
						'total'				=> 	$data["item_3"],
						'total_paymented'	=>	$data["item_3"],
						'item_type'			=>	7,// type other fee vehilce
						'status'			=>	1,
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr);
			}
			
			
			
			// Vehicle info Blog
			if(!empty($data['identity_vehicle'])){
				$ids = explode(',', $data['identity_vehicle']);
				$key=0;
				if(!empty($ids))foreach ($ids as $p){ $key++;
					$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
					$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
					$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
			
					$vehicle_name = $row_vehicle["make"]." ".$row_vehicle["model"]." ".$row_vehicle["sub_model"]." (".$row_vehicle["reffer"].")";
					$discount_ve= empty($discount["discount"])?0:$discount["discount"];
					
					$refund = 0;
					$logg_DAST = 0;
					$dis_val = 0;
					if ($key==1){
						$refund = $total_deposit_vehicle;
						$logg_DAST = $data['long_dast'];
						$dis_val = $data['discount_value'];
					}
					$vehicle_otherFee = ($refund+$logg_DAST)-$dis_val;
					$row_net_total_vehicle = ((($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100))+$vehicle_otherFee;
					$arr_deatail = array(
							'book_id'			=>	$book_id,
							'item_id'			=>	$row_vehicle["id"],
							'item_name'			=>	$vehicle_name,
							'rent_num'			=>	1,
							'price'				=>	$row_vehicle_price["price"],
							'long_dast'			=>	$logg_DAST,
							'discount_value'	=>	$dis_val,
							'VAT'				=>	$row_vehicle_price["vat_value"],
							'total'				=>	$row_net_total_vehicle,
							'total_paymented'	=>	$row_net_total_vehicle,
							'status'			=>	1,
							'refund_deposit'	=>	$refund,//$row_vehicle["refun_deposit"]
							'discount'			=>	$discount_ve,
							'item_type'			=>	1
					);
					
					$this->_name="ldc_booking_detail";
					$this->insert($arr_deatail);
				}
			}
			
			
			
			// Product info Blog
			if(!empty($rows)){
				
				foreach ($rows as $row){
					$total_price = number_format(($row['pro_price']*$row['amount_rent'])*$total_day,2);
					$arr_product = array(
							'book_id'	=>	$book_id,
							'item_id'	=>	$row["product_id"],
							'item_name'	=>	$row["product_name"],
							'rent_num'	=>	$row["amount_rent"],
							'price'		=>	$row['pro_price'],
							'total'		=>	$total_price,
							'VAT'		=>	0,
							'total_paymented'	=>	$total_price,
							'status'	=>	1,
							'item_type'	=>	3
					);
					
					$this->_name="ldc_booking_detail";
					$this->insert($arr_product);
				}
			}
			// Driver Info Blog
			if(!empty($row_driver)){
				
				foreach ($row_driver as $row){
					$refun_deposit_driver = $row["refund_deposit"];
					$total_price = number_format(($row['driver_price']*$row['amount_rent'])*$total_day,2);
					$arr_driver = array(
							'book_id'	=>	$book_id,
							'item_id'	=>	$row["driver_id"],
							'item_name'	=>	$row["driver_name"],
							'rent_num'	=>	$row["amount_rent"],
							'price'		=>	$row['driver_price'],
							'total'		=>	$total_price,
							'VAT'		=>	0,
							'total_paymented'	=>	$total_price,
							'refund_deposit'	=>	$refun_deposit_driver,
							'status'	=>	1,
							'item_type'	=>	2
					);
				
					$this->_name="ldc_booking_detail";
					$this->insert($arr_driver);
				}
			}
			
			//Pickup & Return Info Price Blog
			$arr_pickup = array(
					'book_id'			=>	$book_id,
					'item_id'			=>	$row_pickup["id"],
					'item_name'			=>	"Pickup From ".$pickup_location["province_name"] ."& Return ".$return_location["province_name"],
					'rent_num'			=>	1,
					'price'				=>	$row_pickup['price'],
					'total'				=>	$row_pickup['price']+($row_pickup['price']*$row_pickup["tax"]/100),
					'VAT'				=>	$row_pickup["tax"],
					'total_paymented'	=>	$row_pickup['price']+($row_pickup['price']*$row_pickup["tax"]/100),
					// 					'refund_deposit'	=>	$refun_deposit_driver,
					'status'			=>	1,
					'item_type'			=>	6
			);
			$this->_name="ldc_booking_detail";
			$this->insert($arr_pickup);
			
			$db->commit();
    		return $book_id;
    	}catch (Exception$e){
    		$db->rollBack();
    		echo $e->getMessage();
                Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
	}
	
	function addAgreement($data){
// 		$db = $this->getAdapter();
// 		$db->beginTransaction();
		try{
			$diff=date_diff(date_create($data["pickup_date"]),date_create($data["return_date"]));
			$total_day = $diff->format("%a%")+1;
			
			$db_globle = new Application_Model_DbTable_DbGlobal();
			$agreement_code = $db_globle->getNewAgreementCode($data['agreement_date']);
			
			//Vehicle Blog
			$total_deposit_vehicle=0;
			$total_price_vehicle=0;
			$vat_vehicle = 0;
			if(!empty($data['identity_vehicle'])){
				$ids = explode(',', $data['identity_vehicle']);
				if(!empty($ids))foreach ($ids as $p){
					$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
					$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
					$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
						
					$total_deposit_vehicle+= $row_vehicle["refun_deposit"];
					$total_row_price_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
						
					$total_price_vehicle = number_format(($total_price_vehicle + $total_row_price_vehicle),2);
					$vat_vehicle = $vat_vehicle + $row_vehicle_price["vat_value"];
				}
			}
			$total_payment = $total_price_vehicle+$total_deposit_vehicle;
			$diposit = number_format(($total_payment*50/100)+(($total_payment*50/100)*3/100),2);
			
			if($data["payment_type"]==4){
				$total_pay = $data["cash_pay"];
			}else{
				$total_pay = $diposit;
			}
			
			$arr = array(
					//'vat_owner'=>$data['vat_owner'],
					//'vat_customer'=>$data['vat_customer'],
					'agreement_code'=>$agreement_code,
					'agreement_date'=>$data['agreement_date'],
					'booking_id'=>$data['booking_id'],
					'ownder_id'=>$data['owner_name'],
					'customer_id'=>$data['customer'],
// 					'vehicle_id'=>$data['vehicle_ref_no'],
					'inception_date'=>$data['pickup_date'],
					'return_date'=>$data['return_date'],
					'return_time'=>$data['return_time'].":".$data['return_minute'],
					'period'=>$total_day,
					
					//'price_perday'=>$data['price_day'],
					//'vat_amount'=>$data['total_vat'],
					 
// 					'amount_price'=>$data['amount_price'],
// 					'discount_value'=>$data['discount_value'],
// 					'discount_percent'=>$data['discount_percent'],
// 					'refundable'=>$data['amount_f_d'],
// 					'longdist_acc'=>$data['longdast'],
					'grand_total'=>$total_payment,
					'paid_amount'=>$total_pay,
					'due_amount'=>($total_payment-$total_pay) ,
					 
					'date_create'=>date("d-m-Y"),
					'user_id'=>$this->getUserId(),
					'regular_id'=>$data['regular_maintanance'],
					'unlimited'=>$data['unlimited_mileage'],
					'repare'=>$data['repair_spare_part'],
					'insurance'=>$data['insurance_coverage'],
					'fule'=>$data['fuel'],
					'fuel_full'=>$data['fuel_full_tank'],
					'art1_id'=>$data['article'],
					'toart1_id'=>$data['toart1_id'],
					'art2_id'=>$data['art2_id'],
					'toart2_id'=>$data['toart2_id'],
					'art3_id'=>$data['art3_id'],
					'toart3_id'=>$data['toart3_id'],
					'date_create'=>date("Y-m-d"),
// 					'sunday_price'=>$data['sunday_price'],
// 					'airport_price'=>$data['airport_price'],
// 					'dropairport_price'=>$data['dropairport_price'],
// 					'item_1'=>$data['item_1'],
// 					'item_2'=>$data['item_2'],
// 					'item_3'=>$data['item_3'],
					 
// 					'sunday_remark'=>$data['sunday_remark'],
// 					'airport_remark'=>$data['airport_remark'],
// 					'dropairport_remark'=>$data['dropairport_remark'],
// 					'item_1remark'=>$data['item_1remark'],
// 					'item_2remark'=>$data['item_2remark'],
// 					'item_3remark'=>$data['item_3remark'],
					'is_passport'=>empty($data['passport'])?0:1,
					'is_idcard'=>empty($data['idcard'])?0:1,
					'is_familybook'=>empty($data['familybook'])?0:1,
					'witness'=>$data['witness']
			);
			$this->_name="ldc_agreementvehicle";
			$agreement_id = $this->insert($arr);
			
			// Vehicle info Blog
			if(!empty($data['identity_vehicle'])){
				$ids = explode(',', $data['identity_vehicle']);
				if(!empty($ids))foreach ($ids as $p){
					$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
					$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
					$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
						
					$discount_ve= empty($discount["discount"])?0:$discount["discount"];
					$row_net_total_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
						
					$arr_deatail = array(
							'agreement_id'	=>	$agreement_id,
							'vehicle_id'	=>	$row_vehicle["id"],
							'price_perday'	=>	$row_vehicle_price["price"],
							'vat_amount'	=>	$row_vehicle_price["vat_value"],
							'amount_price'	=>	$row_net_total_vehicle,
							'refundable'	=>	$row_vehicle["refun_deposit"],
							'discount_value'=>	$discount_ve,
// 							'longdist_acc'	=>""
					);
						
					$this->_name="ldc_agreementvehicle_detail";
					$this->insert($arr_deatail);
				}
			}
			
// 			$db->commit();
			return $agreement_id;
		}catch (Exception$e){
// 			$db->rollBack();
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function updateAgreement($data){
		try{
			$diff=date_diff(date_create($data["pickup_date"]),date_create($data["return_date"]));
			$total_day = $diff->format("%a%")+1;
				
			$db_globle = new Application_Model_DbTable_DbGlobal();
			$agreement_code = $db_globle->getNewAgreementCode($data['agreement_date']);
			
			//Vehicle Blog
			$total_deposit_vehicle=0;
			$total_price_vehicle=0;
			$vat_vehicle = 0;
			if(!empty($data['identity_vehicle'])){
				$ids = explode(',', $data['identity_vehicle']);
				if(!empty($ids))foreach ($ids as $p){
					$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
					$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
					$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
	
					$total_deposit_vehicle+= $row_vehicle["refun_deposit"];
					$total_row_price_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
	
					$total_price_vehicle = number_format(($total_price_vehicle + $total_row_price_vehicle),2);
					$vat_vehicle = $vat_vehicle + $row_vehicle_price["vat_value"];
				}
			}
			$total_payment = $total_price_vehicle+$total_deposit_vehicle;
			$diposit = number_format(($total_payment*50/100)+(($total_payment*50/100)*3/100),2);
				
			
			if($data["payment_type"]==4){
				$total_pay = $data["cash_pay"];
			}else{
				$total_pay = $diposit;
			}
			$agreement = $this->getAgreementbyBookingId($data['booking_id']);
			$arr = array(
// 					'agreement_code'=>$agreement_code,
					'agreement_date'=>$data['agreement_date'],
					'booking_id'=>$data['booking_id'],
					'ownder_id'=>$data['owner_name'],
					'customer_id'=>$data['customer'],
					'inception_date'=>$data['pickup_date'],
					'return_date'=>$data['return_date'],
					'return_time'=>$data['return_time'].":".$data['return_minute'],
					'period'=>$total_day,
						
					'grand_total'=>$total_payment,
					'paid_amount'=>$total_pay,
					'due_amount'=>($total_payment-$total_pay) ,
	
					'date_create'=>date("d-m-Y"),
					'user_id'=>$this->getUserId(),
					'regular_id'=>$data['regular_maintanance'],
					'unlimited'=>$data['unlimited_mileage'],
					'repare'=>$data['repair_spare_part'],
					'insurance'=>$data['insurance_coverage'],
					'fule'=>$data['fuel'],
					'fuel_full'=>$data['fuel_full_tank'],
					'art1_id'=>$data['article'],
					'toart1_id'=>$data['toart1_id'],
					'art2_id'=>$data['art2_id'],
					'toart2_id'=>$data['toart2_id'],
					'art3_id'=>$data['art3_id'],
					'toart3_id'=>$data['toart3_id'],
					
					'is_passport'=>empty($data['passport'])?0:1,
					'is_idcard'=>empty($data['idcard'])?0:1,
					'is_familybook'=>empty($data['familybook'])?0:1,
					'witness'=>$data['witness']
						
			);
			$this->_name="ldc_agreementvehicle";
			$where = " booking_id =".$data['booking_id']." AND id = ".$agreement['id'];
			 $this->update($arr, $where);
			 $agreement_id = $agreement['id'];
			$this->_name="ldc_agreementvehicle_detail";
			$where1 = " agreement_id = ".$agreement['id'];
			$this->delete($where1);
			// Vehicle info Blog
			if(!empty($data['identity_vehicle'])){
				$ids = explode(',', $data['identity_vehicle']);
				if(!empty($ids))foreach ($ids as $p){
					$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
					$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
					$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
	
					$discount_ve= empty($discount["discount"])?0:$discount["discount"];
					$row_net_total_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
	
					$arr_deatail = array(
							'agreement_id'	=>	$agreement_id,
							'vehicle_id'	=>	$row_vehicle["id"],
							'price_perday'	=>	$row_vehicle_price["price"],
							'vat_amount'	=>	$row_vehicle_price["vat_value"],
							'amount_price'	=>	$row_net_total_vehicle,
							'refundable'	=>	$row_vehicle["refun_deposit"],
							'discount_value'=>	$discount_ve,
							// 							'longdist_acc'	=>""
					);
	
					$this->_name="ldc_agreementvehicle_detail";
					$this->insert($arr_deatail);
				}
			}
				
			return $agreement_id;
		}catch (Exception$e){
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function getVehicleSelected($id){ //get vehicle was choosed booking
		$db = $this->getAdapter();
		$sql = "SELECT v.`id`,v.ordering AS refun_deposit,(SELECT `title` FROM `ldc_make` WHERE `id`=v.`make_id`) AS make ,(SELECT `title` FROM `ldc_model` WHERE `id`=v.`model_id`) AS model,(SELECT `title` FROM `ldc_submodel` WHERE `id`=v.`sub_model`) AS sub_model ,v.`reffer` FROM `ldc_vehicle` AS v WHERE v.id=$id";
		$row = $db->fetchRow($sql);
		return $row;
	}
	function getVehicleDiscount($id){  //get discount information of vehicle
		$db = $this->getAdapter();
		$date = date("Y-m-d");
		$sql="SELECT v.discount FROM `ldc_vehicle` AS v WHERE '$date' BETWEEN v.`discount_fromdate` AND v.`discount_todate` AND v.`id`=$id";
		return $db->fetchRow($sql);
	}
	function getLocationById($id){ //get location information
		$db = $this->getAdapter();
		$db_gb = new Application_Model_DbTable_DbGlobal();
		$lang= $db_gb->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$sql="SELECT p.`id`,".$array[$lang]." as province_name FROM `ldc_province` AS p WHERE p.`id`=$id";
		return $db->fetchRow($sql);
	}
// 	function getPickUpPrice($data){ // get price location pick
		
// 		$db = $this->getAdapter();
// 		$pickup_id = $data['pickup_location'];
// 		$return_id = $data['return_location'];
	
// 		$sql="SELECT p.`id`,p.`price`,p.`tax` FROM `ldc_pickupcarprice` AS p WHERE p.`form_location`=$pickup_id AND p.`to_location`=$return_id AND p.`vehicle_id`=".$data['vehicle_id'];
// 		return $db->fetchRow($sql);
// 	}
	
	function getPickUpPriceMultiVehicle($data){ // get price location pick
	
		$db = $this->getAdapter();
		$pickup_id = $data['pickup_location'];
		$return_id = $data['return_location'];
		$array= array();
		$price=0;
		$tax=0;
		if(!empty($data['identity_vehicle'])){
			$ids = explode(',', $data['identity_vehicle']);
			if(!empty($ids))foreach ($ids as $p){
				$sql="SELECT p.`id`,p.`price`,p.`tax` FROM `ldc_pickupcarprice` AS p WHERE p.`form_location`=$pickup_id AND p.`to_location`=$return_id AND p.`vehicle_id`=".$data['vehicle_id'.$p];
				$row = $db->fetchRow($sql);
				if (!empty($row)){
					$price+= $row['price'];
					$tax= $row['tax'];
				}
			}
		}
		$array = array(
				'price'=>$price,
				'tax'=>$tax,
				);
		return $array;
	}
	
	function getProductSelected($data){
		if(!empty($data['identity_equipment'])){
			$ids = explode(',', $data['identity_equipment']);
			if(!empty($ids))foreach ($ids as $i){
				$product_id = $this->getProductNameById($data['equipmentid_'.$i]);
				$product_price = $this->getProductPrice($data['equipmentid_'.$i],$data);
				$arr[$i]=array(
						"product_id"=>$data['equipmentid_'.$i],
						'amount_rent'=>$data['number_equipment_'.$i],
						"product_name"=>$product_id,
						"pro_price"	=>	$product_price,
				);
			}
			return $arr;
		}
	}
	function getProductNameById($pro_id){
		$db = $this->getAdapter();
		$sql = " SELECT equipment_name FROM `ldc_stuff` WHERE id =$pro_id LIMIT 1 ";
		return $db->fetchOne($sql);
	}
	function getProductPrice($pro_id,$data){
		$db = $this->getAdapter();
		$pickup_date = date_create($data['pickup_date']);
		$return_date = date_create($data['return_date']);
		$diff=date_diff($pickup_date,$return_date);
		$day = $diff->format("%a%")+1;
	
		$sql_rankday = "SELECT r.`id` FROM `ldc_rankday` AS r WHERE r.`from_amountday`<=$day AND r.`to_amountday`>=$day LIMIT 1";
		$row_randay = $db->fetchOne($sql_rankday);
		if($row_randay){
			$row_randay;
		}else{
			$sql_rankday = "SELECT r.`id` FROM `ldc_rankday` AS r WHERE r.`is_morethen`=1 LIMIT 1";
			$row_randay=$db->fetchOne($sql_rankday);
		}
		$sql_product="SELECT s.`price` FROM `ldc_stuff_details` AS s WHERE s.`stuff_id`=$pro_id AND s.`package_id`=$row_randay LIMIT 1";
		return $db->fetchOne($sql_product);
	}
	function getDriverSelected($data){
		
		if(!empty($data['identity_driver'])){
			$ids = explode(',', $data['identity_driver']);
			
			foreach ($ids as $i){
				
				$product_id = $this->getGuideSelected($data['driverid_'.$i]);
				$item_name = "Chauffeur Rental (".$product_id["driver_name"].")";
				$arr[$i]=array(
						"driver_id"=>$data['driverid_'.$i],
						'amount_rent'=>1,
						"driver_name"=>$item_name,
						"driver_price"	=>	$product_id["p_normalprice"],
						"position_type"	=>	$product_id["position_type"],
						"monthly_price"	=>	$product_id["monthly_price"],
						"refund_deposit"	=>	$product_id["refund_deposit"],
				);
			}
			return $arr;
		}
	}
	public function getGuideSelected($id){
		$db = $this->getAdapter();
		$sql="SELECT CONCAT(d.`first_name`,' ',d.`last_name`) AS driver_name ,d.`p_normalprice`,d.`monthly_price`,d.`position_type`,d.refund_deposit FROM ldc_driver AS d WHERE d.id=$id LIMIT 1";
		$row = $db->fetchRow($sql);
		return $row;
	}
	
	
	function getAllCarBooking($search){
		$db = $this->getAdapter();
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$sql = "SELECT b.`id`,b.`booking_no`,
		(SELECT CONCAT(c.`first_name`,' ',c.`last_name`) FROM `ldc_customer` AS c WHERE c.`id`=b.`customer_id`) AS customer,
		b.`date_book`,CONCAT(b.`pickup_date`,' ',b.`pickup_time`) AS pickup_date,CONCAT(b.`return_date`,' ',b.`return_time`) AS return_date,
		b.`total_fee`,b.`total_paymented`,'View Invoice','View Agreement'
		FROM `ldc_booking` AS b,`ldc_customer` AS c WHERE b.`item_type`=1 AND c.`id`=b.`customer_id` AND b.`date_book`>='$from_date' AND b.`date_book`<='$to_date'";
		$where = '';
		if($search["search_text"] !=""){
			$s_where=array();
    		$s_search=addslashes(trim($search['search_text']));
    		$s_where[]=" b.booking_no LIKE '%{$s_search}%'";
    		$s_where[]=" c.`first_name` LIKE '%{$s_search}%'";
    		$s_where[]=" c.last_name LIKE '%{$s_search}%'";
    		$s_where[]=" c.customer_code LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($search['customer']>0){
			$where.=" AND b.`customer_id`=".$search['customer'];
		}
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	
	function getSearchAvailable($search){
		
		$db_globle = new Application_Model_DbTable_DbGlobal();
		$rowsguide = $db_globle->getAllAvailableGuide($search);
		$vehiclevaliable = $db_globle->getAllAvailableVehicle($search);
		$productavailable= $db_globle->getEquipment($search);
		
		$carlist="";
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$k=0;
		if (!empty($vehiclevaliable)){
			$initail_veh_ide="";
			foreach ($vehiclevaliable as $k_index => $vehicle){ $k++;
			$checked="";
			if ($k_index==0){$checked ='checked="checked"';	$initail_veh_ide =$k;}
			if (!empty($vehicle['img_front'])){
				$image='<img src="'.$baseurl.'/images/vehicle/'.$vehicle['img_front'].'" class="preview_carlist" alt="'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'" />';
			}else{
				$image='<img src="'.$baseurl.'/images/noimage.jpg" class="preview_carlist" alt="'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'" />';
			}
			$rowprice = $db_globle->getVehiceRankingDay($vehicle["id"]);
			$carlist.='
				<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
                	<div class="well profile_view">
						<div class="col-sm-12">
							<h4 class="brief car_title">
								<input '.$checked.'  type="checkbox" id="checkevehiecle'.$k.'" name="checkevehiecle'.$k.'" onClick="addVehicle('.$k.');" >
				                <input type="hidden" name="vehicle_id'.$k.'" value="'.$vehicle["id"].'" />
								'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'
							</h4>
	                            <div class="left carl col-xs-5">
	                            	<div class=" col-xs-12">
	                            		<div class="image_car">
	                            		'.$image.'
	                            		</div>
	                            	</div>
									<div class="clearfix"></div>
                          	  	</div>
                            	<div class=" col-xs-7  text-left">
                             		 <ul class="list-unstyled">
                             		 	<li><div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title">'.$tr->translate("Ranking Day").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title">'.$tr->translate("PRICE").'</span></div></li>';
										if(!empty($rowprice)) foreach($rowprice As $pric){
											$carlist.='
												<li>
			                                		<div class="col-md-6 col-sm-6 col-xs-12"><span class="span_value">'.$pric["package_name"].'</span></div>
			                                		 <div class="col-md-6 col-sm-6 col-xs-12"><span class="span_value color">$ '.number_format($pric["price"],2).'/'.$tr->translate("Day").'</span></div>
			                                		<div class="clearfix"></div>
			                                	</li>
											';
										}
							$carlist.='
									</ul>
								</div>
				           </div>
							<div class="col-xs-12" style="margin-top: 7px;">
	                          	<ul class="list-unstyled">
                              		<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Ref. No.").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["reffer"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Year").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["year"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Color").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["color"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("No. of Seats").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["seat_amount"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Trans. Type").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["transmission"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("VEHICLETYPE").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["type"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Ref. No.").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["reffer"].'</span></div><div class="clearfix"></div></li>
                            	</ul>
                         	 </div>
	                         <div class="col-xs-12 bottom text-center">
	                            <div class="col-xs-12 col-sm-6 emphasis">
	                            </div>
	                            <div class="col-xs-12 col-sm-6 emphasis text-right">
	                               <a class="btn btn-round btn-default" href="'.$baseurl.'/index/vehicle/id/'.$vehicle["id"].'" target="_blank">'.$tr->translate("View Details").'</a>
	                            </div>
	                          </div>
	                        </div>
	                      </div>
							';
						}
		}
		$carlist.='<input type=hidden name="identity_vehicle" id="identity_vehicle" value="'.$initail_veh_ide.'" />';
		$guidlist="";
		$i=0;
		if (!empty($rowsguide)) foreach ($rowsguide as $guid){ $i++;
			if (!empty($guid['photo'])){
				$image='<img src="'.$baseurl.'/images/driverphoto/'.$guid['photo'].'" class="img-circle img-responsive" alt="'.$guid["name"].'" />';
			}else{
				$image='<img src="'.$baseurl.'/images/noimage.jpg" class="img-circle img-responsive" alt="'.$guid["name"].'" />';
			}
			$guidlist.='
			<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
				<div class="well profile_view">
					<div class="col-sm-12">
						<div class="left col-xs-5">
							<div class="col-md-6 col-sm-6 col-xs-12">
							'.$image.'
							</div>
							<div class="clearfix"></div>
	                              <h2>'.$guid["name"].'</h2>
	                               <p><strong>'.$tr->translate("Type").': </strong> '.$guid["position_type_title"].'</p>
	                              <ul class="list-unstyled">
	                                <li> '.$tr->translate("Driver ID").' :'.$guid["driver_id"].'</li>
	                                <li> '.$tr->translate("Nationality").' : '.$guid["nationality"].'</li>
	                                <li> '.$tr->translate("Language").':'.$guid["lang_note"].'</li>
	                              </ul>
                            </div>
                            <div class=" col-xs-7  text-left">
                              <ul class="list-unstyled">
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Nomal Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_normalprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Holiday Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_holidayprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Weekend Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_weekendprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City OT Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_otprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Nomal Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_normalprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Holiday Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_holidayprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Weekend Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_weekendprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              	<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province OT Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_otprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
                              </ul>
                            </div>
                              <div class=" col-xs-12  text-left">
                              	<ul class="list-unstyled">
	                                <li style="line-height: 1.5em;  height: 3em;  overflow: hidden;"> '.$tr->translate("Experience").' : '.$guid["experience_desc"].'</li>
	                              </ul>
                              </div>
                          </div>
                          <div class="col-xs-12 bottom text-center">
	                          <div class="col-xs-12 col-sm-6 emphasis">
	                          </div>
	                          <div class="col-xs-12 col-sm-6 emphasis text-right">
	                          	<a class="btn btn-primary btn-xs"  href="'.$baseurl.'/index/vehicle/id/'.$guid["id"].'" target="_blank"><i class="fa fa-user"> </i> '.$tr->translate("View Details").'</a>
	                         	 <input type="checkbox" name="driver_'.$i.'" class="checkbox input-checkbox" id="driver_'.$i.'" onClick="addDriver('.$i.')" value="'.$guid["id"].'" style="display: inline-block;"/>
	                         	 <input type="hidden" name="driverid_'.$i.'" id="driverid_'.$i.'" value="'.$guid["id"].'" />
             	           </div>
                     </div>
                </div>
              </div>
			';
			
		}
		$guidlist.='<input type=hidden name="identity_driver" id="identity_driver"/>';
			
		$productlist = '';
		$k=0;
		if (!empty($productavailable)) foreach ($productavailable as $rs){ $k++;
			if (!empty($rs["photo_front"])){
				$imagess='<img  src="'.$baseurl.'/images/product/'.$rs["photo_front"].'" class="img-circle img-responsive" alt="'.$rs["equipment_name"].'">';
			}else{
				$imagess='<img src="'.$baseurl.'/images/noimage.jpg" class="img-circle img-responsive" alt="'.$rs["equipment_name"].'">';
			}
			$productlist.='
				<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                    <div class="well profile_view" >
                    	<div class="col-sm-12">
                       	 <h2>
                       	'.$rs["equipment_name"].'
								<input type="hidden" name="equipmentid_'.$k.'" value="'.$rs["id"].'" />
						</h2>
						<div class="clearfix"></div>
                        <div class="left col-xs-4" style=" margin-top: 0;">
                          	'.$imagess.'
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-8 text-left">
	                         <ul class="list-unstyled">
		                        <li style="line-height: 1.5em;  height: 3em;  overflow: hidden;"><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Reference No").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">'.$rs["reference_no"].'</span></div><div class="clearfix"></div></li>
								<li><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("PRICE").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">$ '.number_format($rs["price"],2).'/'.$tr->translate("Day").'</span></div><div class="clearfix"></div></li>
								<li><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Extra Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">$ '.number_format($rs["extra_price"],2).'/'.$tr->translate("Day").'</span></div><div class="clearfix"></div></li>
	                        </ul>
                       </div>
               	 	</div>
               	 	 <div class="col-xs-12 bottom text-center">
	                 	<div class="col-xs-12 col-sm-12 emphasis">
	                 		<ul class="list-unstyled">
		          				<li>
	    							<div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title"><input type="checkbox" name="equipment_'.$k.'" class="checkbox input-checkbox" onClick="addEquipment('.$k.')" id="equipment_'.$k.'" style="display: inline-block;" /> '.$tr->translate("Number of Rent").'</span></div> 
	         						<div class="col-md-6 col-sm-6 col-xs-12"><input onKeyup="calculateGrandtotal();" type="text" class="form-control" name="number_equipment_'.$k.'"  id="number_equipment_'.$k.'" placeholder="'.$tr->translate("Qantity").'"></div>
                                </li>
                          	</ul>
	                   </div>
	              </div>
	          </div>
	       </div>
			';
		}
		$productlist.='<input type="hidden" name="identity_equipment" id="identity_equipment"/>';
		$array = array(
				'vehilce_available'=>$carlist,
				'guide_available'=>$guidlist,
				'product_available'=>$productlist
		);
		
		return $array;
	}
	
	function getBookingListToShow($data){
		
		
		$db_globle = new Application_Model_DbTable_DbGlobal();
		$booking_code = $db_globle->getNewBookingCode();
		
		
		$diff=date_diff(date_create($data["pickup_date"]),date_create($data["return_date"]));
		$total_day = $diff->format("%a%")+1;
		
		$rows = $this->getProductSelected($data);
		$row_driver = $this->getDriverSelected($data);
		
		$pickup_location = $this->getLocationById($data['pickup_location']);
		$return_location = $this->getLocationById($data['return_location']);
		
	
		$cus_name="";
		if (!empty($data['customer'])){
			$customer = $this->getNameCustomer($data['customer']);
			$cus_name = $customer['first_name']." ".$customer['last_name'];
		}
		$string="";
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$string.='
			<table  style="border-collapse:collapse;font-size: 12px;" width="100%">
				<tr>
					<th>
						<table style="border-collapse:collapse;font-size: 12px; width:100% ;text-align: left; padding:5px; line-height: 14px;" cellpadding="5 ">
							<tr style="line-height: 17px;"><td colspan="4" style="border-bottom:1px solid #ccc;text-align: left;background-color: #155f85;padding: 5px 10px;color: #fefafa;"><strong>&nbsp;Booking Info</strong></td></tr>
					        <tr style="line-height: 24px;">
					        	<td style="width:18%">&nbsp;'.$tr->translate("For").' :</td>
					        	<td>'.$cus_name.'</td>
					        	<td style="width:18%">&nbsp;'.$tr->translate("Total Day").'(s) :</td>
					        	<td>'.$total_day.' '.$tr->translate("Day").' (s)</td>
					        </tr>
					        <tr style="line-height: 24px;">
					        	<td style="width:18%">&nbsp;'.$tr->translate("Pickup Date Time").' : </td>
					        	<td>'.date("Y-m-d",strtotime($data["pickup_date"]))." ".$data['pickup_time'].":".$data['pickup_minute'].'</td> 
					        	<td style="width:18%">&nbsp;'.$tr->translate("Pick-up Location").' :</td>
					        	<td>'.$pickup_location["province_name"].'</td>
					        </tr>
				            <tr style="line-height: 24px;">
				            	<td style="width:18%">&nbsp;'.$tr->translate("Return Date Time").'</td>
				            	<td>'.date("Y-m-d",strtotime($data["return_date"]))." ".$data['return_time'].":".$data['return_minute'].'</td>
				            	<td style="width:18%">&nbsp;'.$tr->translate("Return Location").' :</td>
				            	<td>'.$return_location["province_name"].'</td>
				            </tr>
				            <tr></tr>
			       	 	</table>
			        </th>
				</tr>
				<tr >
			    </tr>
			</table>
            <table style="border-collapse:collapse;font-size: 12px;margin-top:10px;float: left;" width="100%">
				<tr><td colspan="7" style="border-bottom:1px solid #ccc;text-align: left;background-color: #155f85;padding: 5px 10px;color: #fefafa;"><strong>&nbsp;Item Info</strong></td></tr>
				<tr>
				     <th rowspan="2" width="5%" class="totalbr text-center">No</th>
				     <th rowspan="2" width="50%" class="totalbr text-center" >Items Description</th> 
				     <th rowspan="2" width="10%" class="totalbr text-center" >QTY</th> 
				     <th colspan="4" width="35%" class="totalbr text-center">Price In US ($)</th>
				</tr>
				<tr style="text-align: center">
				     <th class="totalbr text-center" width="10%">Price</th>
				     <th class="totalbr text-center" width="5%">VAT</th>
				     <th class="totalbr text-center" width="5%">Discount</th>
				     <th class="totalbr text-center" width="25%" nowrap="nowrap">Amount</th>
				  </tr>';

		//row vehicle
		$refun_deposit=0;
		$net_total_vehicle=0;
		if(!empty($data['identity_vehicle'])){
			$ids = explode(',', $data['identity_vehicle']);
			if(!empty($ids))foreach ($ids as $p){
				$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
				$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
				
				$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
				$vehicle_name = $row_vehicle["make"]." ".$row_vehicle["model"]." ".$row_vehicle["sub_model"]." (".$row_vehicle["reffer"].")";
				$refun_deposit+= $row_vehicle["refun_deposit"];
				$discount_ve= empty($discount["discount"])?0:$discount["discount"];
				
				$row_net_total_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
				$net_total_vehicle = $net_total_vehicle + $row_net_total_vehicle;
				$string.='
				<tr>
					<td class="totalbr text-center">1</td>
					<td class="totalbr" style="text-align: left; !important;">'.$vehicle_name.'</td>
					<td class="totalbr" style="text-align: center; !important;">1</td>
					<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($row_vehicle_price["price"],2).'</td>
					<td class="totalbr" align="right" style="padding-right: 10px">'.number_format($row_vehicle_price["vat_value"],2).'%</td>
					<td class="totalbr" align="right" style="padding-right: 10px">'.number_format($discount_ve,2).'%</td>
					<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($row_net_total_vehicle,2).'</td>
				</tr>';
			}
		}
		if ($data['refund_type']==2){
			$refun_deposit = $data['refundable_deposit'];
		}
		$vehicle_otherFee = 0;
		$vehicle_otherFee = ($refun_deposit+$data['long_dast'])-$data['discount_value'];
		
		
		// Product info Blog
		$net_total_prodcut=0;
		$i=0;
		if(!empty($rows)){
			foreach ($rows as $num => $row){
				$i = $num+1;
				$total_price = ($row['pro_price']*$row['amount_rent'])*$total_day;
				$net_total_prodcut += $total_price;
				$string.='
					<tr>
						<td class="totalbr text-center">'.($num+1).'</td>
                        <td class="totalbr" style="text-align: left; !important;">'.$row['product_name'].'</td>
                        <td class="totalbr" style="text-align: center; !important;">'.$row['amount_rent'].'</td>
                        <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($row['pro_price'],2).'</td>
                        <td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
                        <td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
                        <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($total_price,2).'</td>
					</tr>
				';
			}
		}
		
		// Driver Info Blog
		$refun_deposit_driver=0;
		$net_total_driver=0;
		$j=$i;
		if(!empty($row_driver)){
			foreach ($row_driver as $num => $row){
				$refun_deposit_driver += $row["refund_deposit"];
				$total_price = ($row['driver_price']*$row['amount_rent'])*$total_day;
				$net_total_driver += $total_price;
				$j = $i+$num;
				$string.='
					<tr>
						 <td class="totalbr text-center">'.($i+$num).'</td>
                         <td class="totalbr" style="text-align: left; !important;">'.$row['driver_name'].'</td>
                    	 <td class="totalbr" style="text-align: center; !important;">'.$row['amount_rent'].'</td>
                         <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($row['driver_price'],2).'</td>
                         <td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
                         <td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
                         <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($total_price,2).'</td>
					</tr>
				';
			}
		}
		
		$aj = $j;
		//Other fee vehicle
		$Extra_ch_sun = 0;
		if($data["sunday_price"]>0){
			$Extra_ch_sun = $data["sunday_price"];
			$string.='
			<tr>
				<td class="totalbr text-center">'.($j+1).'</td>
				<td class="totalbr" style="text-align: left; !important;">Extra Charge Sunday | '.$data["sunday_price_remake"].'</td>
				<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["sunday_price"],2).'</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["sunday_price"],2).'</td>
			</tr>
			';
		}
		$airport_price=0;
		if($data["airport_price"]>0){
			$airport_price = $data["airport_price"];
			$string.='
			<tr>
				<td class="totalbr text-center">'.($j+1).'</td>
				<td class="totalbr" style="text-align: left; !important;">Pickup Airport | '.$data["airport_price_remake"].'</td>
				<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["airport_price"],2).'</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["airport_price"],2).'</td>
			</tr>
			';
		}
		$dropairport_price=0;
		if($data["dropairport_price"]>0){
			$dropairport_price = $data["dropairport_price"];
			$string.='
			<tr>
				<td class="totalbr text-center">'.($j+1).'</td>
				<td class="totalbr" style="text-align: left; !important;">Drop of Airport | '.$data["dropairport_price_remake"].'</td>
				<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["dropairport_price"],2).'</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["dropairport_price"],2).'</td>
			</tr>
			';
		}
		$item_1=0;
		if($data["item_1"]>0){
			$item_1 = $data["item_1"];
			$string.='
			<tr>
				<td class="totalbr text-center">'.($j+1).'</td>
				<td class="totalbr" style="text-align: left; !important;">'.$data["item_1_remake"].'</td>
				<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_1"],2).'</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
				<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_1"],2).'</td>
			</tr>
			';
		}
		$item_2=0;
		if($data["item_2"]>0){
			$item_2 = $data["item_2"];
			$string.='
			<tr>
			<td class="totalbr text-center">'.($j+1).'</td>
			<td class="totalbr" style="text-align: left; !important;">'.$data["item_2_remake"].'</td>
			<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
			<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_2"],2).'</td>
			<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
			<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
			<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_2"],2).'</td>
			</tr>
			';
		}
		$item_3=0;
		if($data["item_3"]>0){
			$item_3 = $data["item_3"];
			$string.='
			<tr>
			<td class="totalbr text-center">'.($j+1).'</td>
			<td class="totalbr" style="text-align: left; !important;">'.$data["item_3_remake"].'</td>
			<td class="totalbr" style="text-align: center; !important;">&nbsp;</td>
			<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_3"],2).'</td>
			<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0,2).'%</td>
			<td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
			<td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($data["item_3"],2).'</td>
			</tr>
			';
		}
		
		
		$x = $aj;
		//Pickup & Return Info Price Blog
		$row_pickup =$this->getPickUpPriceMultiVehicle($data);
		$net_total_pickup = ($row_pickup["price"]+($row_pickup["price"]*$row_pickup["tax"]/100));
		$string.='
			<tr>
				<td class="totalbr text-center">'.($aj+1).'</td>
                <td class="totalbr" style="text-align: left; !important;">'."Pickup From ".$pickup_location["province_name"] ."& Return ".$return_location["province_name"].'</td>
                <td class="totalbr" style="text-align: center; !important;">1</td>
                <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($row_pickup["price"],2).'</td>
                <td class="totalbr" align="right" style="padding-right: 10px">'.number_format($row_pickup["tax"],2).'%</td>
                <td class="totalbr" align="right" style="padding-right: 10px">'.number_format(0).'%</td>
                <td class="totalbr" align="right" style="padding-right: 10px">$ '.number_format($net_total_pickup,2).'</td>
			</tr>
		';
		
// 		$net_total= $net_total_vehicle+$net_total_pickup+$net_total_driver+$net_total_prodcut+$net_total_other_fee;
		$net_total= $net_total_vehicle+$net_total_pickup+$net_total_driver+$net_total_prodcut+$vehicle_otherFee+$Extra_ch_sun+$airport_price+$dropairport_price+$item_1+$item_2+$item_3;
		$total_refun = $refun_deposit+$refun_deposit_driver;
		$book = round(($net_total*50/100)+(($net_total*50/100)*3/100),2);
		$grand_total=$net_total+$total_refun;
		$string.='
			<tr>
			  <td ></td>
			  <td></td>
			  <td></td>
			  <td class="totalbr" colspan="2">&nbsp;1. Rental Fee:</td>
			  <td style="font-weight: 800;text-align: right" class="totalbr" colspan="2">&nbsp;$ '.number_format($net_total,2).'</td>
		  	</tr>
		 	<tr>
			  <td ></td>
			  <td></td>
			  <td></td>
			  <td class="totalbr" colspan="2">&nbsp;2. Refundable Deposit:</td>
			  <td id="refund_deposit_view" style="font-weight: 800;text-align: right" class="totalbr" colspan="2">&nbsp;$ '.number_format($total_refun,2).'</td>
		   </tr>
		   <tr>
			  <td ></td>
			  <td></td>
			  <td></td>
			  <td style="white-space: nowrap;" class="totalbr" colspan="2">&nbsp;3. Amount Paid (<span style="font-size:10px;">50%​ + Bank Charge 3%</span>):</td>
			  <td style="font-weight: 800;text-align: right" class="totalbr" colspan="2">&nbsp;$ '.number_format($book,2).'</td>
		   </tr>
		   <tr>
			  <td ></td>
			  <td></td>
			  <td></td>
			  <td class="totalbr" colspan="2">&nbsp;4. Net Total:</td>
			  <td style="font-weight: 800;text-align: right" class="totalbr" colspan="2">&nbsp;$ '.number_format($grand_total,2).'</td>
		   </tr>
		   <tr>
			  <td ></td>
			  <td></td>
			  <td></td>
			  <td class="totalbr" colspan="2">&nbsp;5. Due Amount:</td>
			  <td style="font-weight: 800; text-align: right" class="totalbr" colspan="2">&nbsp;$ '.number_format($grand_total-$book,2).'</td>
		  </tr>
	   </table>
		';
		$array = array(
				'bookinglist'=>$string,
				'g_total'=>$grand_total,
				'refund_deposite'=>$refun_deposit,
				'rentvehiclefee'=>$net_total_vehicle,
				);
		
		return $array;
	}
	function getNameCustomer($client_id){
		$sql="SELECT c.id,c.`customer_code`,c.`first_name`,c.`last_name`,c.`email`,c.`phone`,c.`sex` FROM ldc_customer AS c WHERE id=".$client_id;
		$order=' LIMIT 1';
		return $this->getAdapter()->fetchRow($sql.$order);
	}
	
	//edit booking
	function getBookingById($id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_booking` AS b WHERE b.`id`=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAgreementbyBookingId($bookingid){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_agreementvehicle` AS agv WHERE agv.`booking_id` =$bookingid";
		return $db->fetchRow($sql);
	}
	function getProductBookingInfor($bookingid,$items_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_booking_detail` AS bd WHERE bd.`book_id`=$bookingid AND bd.`item_id`=$items_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getBookingDetail($booking_id,$itemstype=null){
		$db = $this->getAdapter();
		$sql="SELECT bd.* FROM`ldc_booking_detail` AS bd WHERE bd.`book_id`=$booking_id ";
		if (!empty($itemstype)){
			$sql.=" AND bd.item_type=$itemstype";
		}
		return $db->fetchAll($sql);
	}
	function getVehicelByBookingId($booking_id,$onlyid=null){
		$db = $this->getAdapter();
		$sql="SELECT bd.* FROM`ldc_booking_detail` AS bd WHERE bd.`book_id`=$booking_id AND bd.item_type=1 ";
		$row = $db->fetchAll($sql);
		if (empty($onlyid)){
			return $row;
		}else{
			$idlist="";
			foreach ($row as $rs){
				if (empty($idlist)){
					if (!empty($rs['item_id'])){
						$idlist=$rs['item_id'];
					}
				}else{
					if (!empty($rs['item_id'])){
						$idlist=$idlist.",".$rs['item_id'];
					}
				}
			}
			return $idlist;
		}
	}
	public function getAllAvailableVehicleForEdit($data){
		$db = $this->getAdapter();
		$pickup_date = new DateTime($data["pickup_date"]);
		$return_date = new DateTime($data["return_date"]);
		$pickupdate = $pickup_date->format('Y-m-d'); // 2017-11-20
		$returndate = $return_date->format('Y-m-d');
			
		// 		$pickuptime = $data["pickup_time"];
		$returntime = $data["return_time"];
			$sql = "SELECT v.id,v.`reffer`,v.`frame_no`,v.`max_weight`,
			v.`seat_amount`,v.`color`,v.`year`,v.`steering`,v.`test_url`,v.`show_url`,
			v.`img_front`,
			v.`img_front_right`,v.img_seat,
			v.`is_promotion`,v.`discount`,(SELECT m.title FROM `ldc_make` AS m WHERE m.id=v.`make_id`)
			AS make,(SELECT md.title FROM `ldc_model` AS md WHERE md.id=v.`model_id`) AS model,
			(SELECT sm.title FROM `ldc_submodel` AS sm WHERE sm.id=v.`sub_model`) AS sub_model,
			(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
			(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
			(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine` FROM `ldc_vehicle` AS v WHERE v.is_sale !=1 AND v.status=1 AND v.id
			NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND
			('$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date`
			OR '$returndate ' BETWEEN b.`pickup_date` AND b.`return_date`)
			AND bd.item_type=1 AND b.status!=3)"; // it wiil include with new version AND b.`return_time` >= '$returntime'
			if (!empty($data['id'])){
				$bookinginfo = $this->getBookingById($data['id']);
				if (date("Y-m-d",strtotime($bookinginfo['return_date'])) < date("Y-m-d",strtotime($data['pickup_date']))){
					
				}else{
					$vehicle_id = $this->getVehicelByBookingId($data['id'],1);
					if (!empty($vehicle_id)){
						$sql.=" OR v.id IN (".$vehicle_id.")";
					}
				}
			}
			
			$row = $db->fetchAll($sql);
			if(!empty($row)){
				return $db->fetchAll($sql);
			}
		}
		
		function getGuidByBookingId($booking_id,$onlyid=null){
			$db = $this->getAdapter();
			$sql="SELECT bd.* FROM`ldc_booking_detail` AS bd WHERE bd.`book_id`=$booking_id AND bd.item_type=2 ";
			$row = $db->fetchAll($sql);
			if (empty($onlyid)){
				return $row;
			}else{
				$idlist="";
				foreach ($row as $rs){
					if (empty($idlist)){
						if (!empty($rs['item_id'])){
							$idlist=$rs['item_id'];
						}
					}else{
						if (!empty($rs['item_id'])){
							$idlist=$idlist.",".$rs['item_id'];
						}
					}
				}
				return $idlist;
			}
		}
		public function getAllAvailableGuideEdit($data,$type=3){ // 1=driver,2=guide,3=both
			$db= $this->getAdapter();
			$pickup_date = new DateTime($data["pickup_date"]);
			$return_date = new DateTime($data["return_date"]);
			$pickupdate = $pickup_date->format('Y-m-d'); // 2003-10-16
			$returndate = $return_date->format('Y-m-d');
			$returntime = $data["return_time"];
			if($type==1){
				$position_type = " AND d.`position_type`=1";
			}elseif ($type==2){
				$position_type = " AND d.`position_type`=2";
			}else {
				$position_type = "";
			}
			$sql="SELECT d.`id`,d.`driver_id`,CONCAT(d.`first_name`,' ',d.`last_name`) AS `name`,d.`experience_desc`,d.`sex`,d.`nationality`,d.`lang_note`,d.`tel`,d.`email`,d.`photo`,d.`c_holidayprice`,d.`c_normalprice`,d.`c_otprice`,d.`c_weekendprice`,d.`p_holidayprice`,d.`p_normalprice`,d.`p_otprice`,d.`p_weekendprice`,d.`monthly_price`,d.`position_type`,
			(SELECT lv.name_en FROM `ldc_view` AS lv WHERE lv.key_code=d.`position_type` AND lv.type =8 LIMIT 1) AS position_type_title
			FROM `ldc_driver` AS d WHERE  d.`status`=1 AND  d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND b.`return_date` BETWEEN '$pickupdate' AND '$returndate'  AND bd.item_type=2 AND b.status !=3) 
			 $position_type"; // Will Include with new Version AND b.`return_time` >= '$returntime'
			
			if (!empty($data['id'])){
				$guide_id = $this->getGuidByBookingId($data['id'],1);
				if (!empty($guide_id)){
					$sql.=" OR d.id IN (".$guide_id.")";
				}
			}
			$row = $db->fetchAll($sql);
			if (!empty($row)) {
				return $row;
			}
			return "";
		}
		public function editBookingRental($data){
			$db = $this->getAdapter();
			$db->beginTransaction();
			try{
				$db_globle = new Application_Model_DbTable_DbGlobal();
		
		
				$diff=date_diff(date_create($data["pickup_date"]),date_create($data["return_date"]));
				$total_day = $diff->format("%a%")+1;
				 
		
				$rows = $this->getProductSelected($data);
				$row_driver = $this->getDriverSelected($data);
		
				$pickup_location = $this->getLocationById($data['pickup_location']);
				$return_location = $this->getLocationById($data['return_location']);
				$row_pickup =$this->getPickUpPriceMultiVehicle($data);
		
		
				//Vehicle Blog
				$total_deposit_vehicle=0;
				$total_price_vehicle=0;
				$vat_vehicle = 0;
				if(!empty($data['identity_vehicle'])){
					$ids = explode(',', $data['identity_vehicle']);
					if(!empty($ids))foreach ($ids as $p){
						$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
						$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
						$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
		
						$total_deposit_vehicle+= $row_vehicle["refun_deposit"];
						$total_row_price_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
		
						$total_price_vehicle = number_format(($total_price_vehicle + $total_row_price_vehicle),2);
						$vat_vehicle = $vat_vehicle + $row_vehicle_price["vat_value"];
					}
				}
		
				//Product blog
				$total_price_product =0;
				if(!empty($rows)){
					foreach ($rows as $row){
						$total_price_product+= number_format(($row['pro_price']*$row['amount_rent'])*$total_day,2);
					}
				}
		
				// Driver Blog
				$total_price_driver = 0;
				if(!empty($row_driver)){
					foreach ($row_driver as $row){
						$total_price_driver+= number_format(($row['driver_price']*$row['amount_rent'])*$total_day,2);
					}
				}
				// other fee Blog
				$total_other_fee = 0;
				if($data["identity_other_fee"]!=""){
					$ids = explode(',', $data['identity_other_fee']);
					foreach ($ids as $i){
						$total_other_fee+=$data["other_fee".$i];
					}
				}
				//Pickup & Return Blog
				$total_price_pickup = number_format($row_pickup["price"]+($row_pickup["price"]*$row_pickup["tax"]/100),2);
				$vat_pickup = $row_pickup["tax"];
				///
				$total_payment = $total_price_vehicle+$total_price_driver+$total_price_product+$total_price_pickup+$total_other_fee;
				$diposit = number_format(($total_payment*50/100)+(($total_payment*50/100)*3/100),2);
		
		
				if($data["payment_type"]==4){
					$total_fee = $total_payment;
					$deposit_fee = 0;
					$total_pay = $data["cash_pay"];
				}else{
					$total_fee = $total_payment;
					$deposit_fee = $diposit;
					$total_pay = $diposit;
				}
		
				$arr = array(
						'customer_id'			=>	$data["customer"],
// 						'date_book'			=>	date("Y-m-d"),
						'pickup_date'			=>	date("Y-m-d",strtotime($data["pickup_date"])),
						'pickup_time'			=>	$data['pickup_time'].":".$data['pickup_minute'],
						'return_date'			=>	date("Y-m-d",strtotime($data["return_date"])),
						'return_time'			=>	$data['return_time'].":".$data['return_minute'],
						'total_fee'				=>	$total_fee,
						'total_paymented'		=>	$total_pay,
						'item_type'				=>	1,
						//'rental_type'		=>	$rental_type,
						//'total_duration'	=>	$session->duration,
						'pickup_location'		=>	$pickup_location["id"],
						'dropoff_location'		=>	$return_location["id"],
						'fly_no'				=>	$data["fly_no"],
						'fly_date'				=>	date("Y-m-d",strtotime($data["fly_date"])),
						'fly_time_of_arrival'	=>	$data["fly_time"],
						'fly_destination'		=>	$data["fly_destination"],
						'status'				=>	1,
						'deposite_fee'			=>	$deposit_fee,
						'total_vat'				=>	$vat_pickup+$vat_vehicle,
						'user_id'				=>	$this->getUserId(),
						'modify_date'			=>	date("Y-m-d H:i:s"),
				);
					
				if($data["payment_type"]==1){
					$arr['visa_name']= $data["card_name"];
					$arr['card_id']  = $data["card_id"];
					$arr['secu_code']= $data["secu_code"];
					$arr['card_exp_date']=$data["card_exp_date"];
					$arr['card_id']  = $data["card_id"];
					$arr['payment_type']=1;
						
				}elseif($data["payment_type"]==2){
					$arr['card_id']=$data["wu_code"];
					$arr['payment_type']=2;
				}elseif($data["payment_type"]==3){
					$arr['payment_type']=3;
				}elseif($data["payment_type"]==4){
					$arr['payment_type']=4;
				}
					
				$this->_name = "ldc_booking";
				$where = ' id = '.$data['id'];
				$this->update($arr, $where);
				$book_id = $data['id'];
			
				$data['booking_id'] = $data['id'];
				$this->updateAgreement($data);
				
				//delete old Detail booking
				$this->_name="ldc_booking_detail";
				$where1 = " book_id = ".$data['id'];
				$this->delete($where1);
				
				
				//insert new Detail booking
				
				//Other fee blog
				if($data["identity_other_fee"]!=""){
					$ids = explode(',', $data['identity_other_fee']);
		
					foreach ($ids as $i){
						$arr = array(
								'book_id'			=>	$book_id,
								'item_name'			=>	$data["other_fee_note".$i],
								'price'				=>	$data["other_fee".$i],
								'total'				=> 	$data["other_fee".$i],
								'total_paymented'	=>	$data["other_fee".$i],
								'item_type'			=>	7,
								'status'			=>	1,
						);
						$this->_name="ldc_booking_detail";
						$this->insert($arr);
					}
				}
					
				// Vehicle info Blog
				if(!empty($data['identity_vehicle'])){
					$ids = explode(',', $data['identity_vehicle']);
					if(!empty($ids))foreach ($ids as $p){
						$row_vehicle = $this->getVehicleSelected($data['vehicle_id'.$p]);
						$discount = $this->getVehicleDiscount($data['vehicle_id'.$p]);
						$row_vehicle_price = $db_globle->getVehiclePrice($total_day, $data['vehicle_id'.$p]);
							
						$vehicle_name = $row_vehicle["make"]." ".$row_vehicle["model"]." ".$row_vehicle["sub_model"]." (".$row_vehicle["reffer"].")";
						$discount_ve= empty($discount["discount"])?0:$discount["discount"];
						$row_net_total_vehicle = (($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$discount["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100);
							
						$arr_deatail = array(
								'book_id'			=>	$book_id,
								'item_id'			=>	$row_vehicle["id"],
								'item_name'			=>	$vehicle_name,
								'rent_num'			=>	1,
								'price'				=>	$row_vehicle_price["price"],
								'VAT'				=>	$row_vehicle_price["vat_value"],
								'total'				=>	$row_net_total_vehicle,
								'total_paymented'	=>	$row_net_total_vehicle,
								'status'			=>	1,
								'refund_deposit'	=>	$row_vehicle["refun_deposit"],
								'discount'			=>	$discount_ve,
								'item_type'			=>	1
						);
							
						$this->_name="ldc_booking_detail";
						$this->insert($arr_deatail);
					}
				}
					
					
					
				// Product info Blog
				if(!empty($rows)){
		
					foreach ($rows as $row){
						$total_price = number_format(($row['pro_price']*$row['amount_rent'])*$total_day,2);
						$arr_product = array(
								'book_id'	=>	$book_id,
								'item_id'	=>	$row["product_id"],
								'item_name'	=>	$row["product_name"],
								'rent_num'	=>	$row["amount_rent"],
								'price'		=>	$row['pro_price'],
								'total'		=>	$total_price,
								'VAT'		=>	0,
								'total_paymented'	=>	$total_price,
								'status'	=>	1,
								'item_type'	=>	3
						);
							
						$this->_name="ldc_booking_detail";
						$this->insert($arr_product);
					}
				}
				// Driver Info Blog
				if(!empty($row_driver)){
		
					foreach ($row_driver as $row){
						$refun_deposit_driver = $row["refund_deposit"];
						$total_price = number_format(($row['driver_price']*$row['amount_rent'])*$total_day,2);
						$arr_driver = array(
								'book_id'	=>	$book_id,
								'item_id'	=>	$row["driver_id"],
								'item_name'	=>	$row["driver_name"],
								'rent_num'	=>	$row["amount_rent"],
								'price'		=>	$row['driver_price'],
								'total'		=>	$total_price,
								'VAT'		=>	0,
								'total_paymented'	=>	$total_price,
								'refund_deposit'	=>	$refun_deposit_driver,
								'status'	=>	1,
								'item_type'	=>	2
						);
		
						$this->_name="ldc_booking_detail";
						$this->insert($arr_driver);
					}
				}
					
				//Pickup & Return Info Price Blog
				$arr_pickup = array(
						'book_id'			=>	$book_id,
						'item_id'			=>	$row_pickup["id"],
						'item_name'			=>	"Pickup From ".$pickup_location["province_name"] ."& Return ".$return_location["province_name"],
						'rent_num'			=>	1,
						'price'				=>	$row_pickup['price'],
						'total'				=>	$row_pickup['price']+($row_pickup['price']*$row_pickup["tax"]/100),
						'VAT'				=>	$row_pickup["tax"],
						'total_paymented'	=>	$row_pickup['price']+($row_pickup['price']*$row_pickup["tax"]/100),
						// 					'refund_deposit'	=>	$refun_deposit_driver,
						'status'			=>	1,
						'item_type'			=>	6
				);
				$this->_name="ldc_booking_detail";
				$this->insert($arr_pickup);
					

				$db->commit();
				return $book_id;
			}catch (Exception$e){
				$db->rollBack();
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		function getSearchAvailableEdit($search){
		
			$db_globle = new Application_Model_DbTable_DbGlobal();
			$rowsguide = $this->getAllAvailableGuideEdit($search);
			$vehiclevaliable = $this->getAllAvailableVehicleForEdit($search);
			$productavailable= $db_globle->getEquipment($search);
		
			$carlist="";
			$tr = Application_Form_FrmLanguages::getCurrentlanguage();
			$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
			$k=0;
			if (!empty($vehiclevaliable)){
				$initail_veh_ide="";
				foreach ($vehiclevaliable as $k_index => $vehicle){
					$k++;
					$checked="";
					if ($k_index==0){
						$checked ='checked="checked"';	$initail_veh_ide =$k;
					}
					if (!empty($vehicle['img_front'])){
						$image='<img src="'.$baseurl.'/images/vehicle/'.$vehicle['img_front'].'" class="preview_carlist" alt="'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'" />';
					}else{
						$image='<img src="'.$baseurl.'/images/noimage.jpg" class="preview_carlist" alt="'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'" />';
					}
					$rowprice = $db_globle->getVehiceRankingDay($vehicle["id"]);
					$carlist.='
					<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
						<div class="well profile_view">
							<div class="col-sm-12">
								<h4 class="brief car_title">
									<input '.$checked.'  type="checkbox" id="checkevehiecle'.$k.'" name="checkevehiecle'.$k.'" onClick="addVehicle('.$k.');" >
									<input type="hidden" name="vehicle_id'.$k.'" value="'.$vehicle["id"].'" />
								'.$vehicle["make"].' '.$vehicle["model"].' '.$vehicle["sub_model"].' ('.$vehicle["reffer"].')'.'
								</h4>
								<div class="left carl col-xs-5">
									<div class=" col-xs-12">
										<div class="image_car">
										'.$image.'
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class=" col-xs-7  text-left">
									<ul class="list-unstyled">
										<li><div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title">'.$tr->translate("Ranking Day").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title">'.$tr->translate("PRICE").'</span></div></li>';
										if(!empty($rowprice)) foreach($rowprice As $pric){
											$carlist.='
										<li>
											<div class="col-md-6 col-sm-6 col-xs-12"><span class="span_value">'.$pric["package_name"].'</span></div>
											<div class="col-md-6 col-sm-6 col-xs-12"><span class="span_value color">$ '.number_format($pric["price"],2).'/'.$tr->translate("Day").'</span></div>
											<div class="clearfix"></div>
										</li>';
								}
						$carlist.='
									</ul>
								</div>
							</div>
							<div class="col-xs-12" style="margin-top: 7px;">
								<ul class="list-unstyled">
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Ref. No.").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["reffer"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Year").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["year"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Color").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["color"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("No. of Seats").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["seat_amount"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Trans. Type").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["transmission"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("VEHICLETYPE").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["type"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-4 col-sm-4 col-xs-12"><span class="span_title">'.$tr->translate("Ref. No.").'</span></div> <div class="col-md-8 col-sm-8 col-xs-12">: <span class="span_value">'.$vehicle["reffer"].'</span></div><div class="clearfix"></div></li>
								</ul>
							</div>
							<div class="col-xs-12 bottom text-center">
								<div class="col-xs-12 col-sm-6 emphasis">
								</div>
								<div class="col-xs-12 col-sm-6 emphasis text-right">
									<a class="btn btn-round btn-default" href="'.$baseurl.'/index/vehicle/id/'.$vehicle["id"].'" target="_blank">'.$tr->translate("View Details").'</a>
								</div>
							</div>
						</div>
					</div>
					';
				}
			}
			$carlist.='<input type=hidden name="identity_vehicle" id="identity_vehicle" value="'.$initail_veh_ide.'" />';
			$guidlist="";
			$i=0;
			if (!empty($rowsguide)) foreach ($rowsguide as $guid){
				$i++;
				if (!empty($guid['photo'])){
					$image='<img src="'.$baseurl.'/images/driverphoto/'.$guid['photo'].'" class="img-circle img-responsive" alt="'.$guid["name"].'" />';
				}else{
					$image='<img src="'.$baseurl.'/images/noimage.jpg" class="img-circle img-responsive" alt="'.$guid["name"].'" />';
				}
				$guidlist.='
				<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
					<div class="well profile_view">
						<div class="col-sm-12">
							<div class="left col-xs-5">
								<div class="col-md-6 col-sm-6 col-xs-12">
								'.$image.'
								</div>
								<div class="clearfix"></div>
								<h2>'.$guid["name"].'</h2>
								<p><strong>'.$tr->translate("Type").': </strong> '.$guid["position_type_title"].'</p>
								<ul class="list-unstyled">
									<li> '.$tr->translate("Driver ID").' :'.$guid["driver_id"].'</li>
									<li> '.$tr->translate("Nationality").' : '.$guid["nationality"].'</li>
									<li> '.$tr->translate("Language").':'.$guid["lang_note"].'</li>
								</ul>
							</div>
							<div class=" col-xs-7  text-left">
								<ul class="list-unstyled">
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Nomal Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_normalprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Holiday Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_holidayprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City Weekend Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_weekendprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("City OT Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["c_otprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Nomal Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_normalprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Holiday Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_holidayprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province Weekend Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_weekendprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
									<li> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Province OT Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_value">: $ '.number_format($guid["p_otprice"],2).'/'.$tr->translate("Day").'</span></div> <div class="clearfix"></div></li>
								</ul>
							</div>
							<div class=" col-xs-12  text-left">
								<ul class="list-unstyled">
									<li style="line-height: 1.5em;  height: 3em;  overflow: hidden;"> '.$tr->translate("Experience").' : '.$guid["experience_desc"].'</li>
								</ul>
							</div>
						</div>
						<div class="col-xs-12 bottom text-center">
							<div class="col-xs-12 col-sm-6 emphasis">
							</div>
							<div class="col-xs-12 col-sm-6 emphasis text-right">
								<a class="btn btn-primary btn-xs"  href="'.$baseurl.'/index/vehicle/id/'.$guid["id"].'" target="_blank"><i class="fa fa-user"> </i> '.$tr->translate("View Details").'</a>
								<input type="checkbox" name="driver_'.$i.'" class="checkbox input-checkbox" id="driver_'.$i.'" onClick="addDriver('.$i.')" value="'.$guid["id"].'" style="display: inline-block;"/>
								<input type="hidden" name="driverid_'.$i.'" value="'.$guid["id"].'" />
							</div>
						</div>
					</div>
				</div>
				';
					
			}
			$guidlist.='<input type=hidden name="identity_driver" id="identity_driver"/>';
			$productlist = '';
			$k=0;
			if (!empty($productavailable)) foreach ($productavailable as $rs){
				$k++;
				if (!empty($rs["photo_front"])){
					$imagess='<img  src="'.$baseurl.'/images/product/'.$rs["photo_front"].'" class="img-circle img-responsive" alt="'.$rs["equipment_name"].'">';
				}else{
					$imagess='<img src="'.$baseurl.'/images/noimage.jpg" class="img-circle img-responsive" alt="'.$rs["equipment_name"].'">';
				}
				$productlist.='
				<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
					<div class="well profile_view" >
						<div class="col-sm-12">
							<h2>
								'.$rs["equipment_name"].'
								<input type="hidden" name="equipmentid_'.$k.'" value="'.$rs["id"].'" />
							</h2>
							<div class="clearfix"></div>
							<div class="left col-xs-4" style=" margin-top: 0;">
								'.$imagess.'
								<div class="clearfix"></div>
							</div>
							<div class="col-xs-8 text-left">
								<ul class="list-unstyled">
									<li style="line-height: 1.5em;  height: 3em;  overflow: hidden;"><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Reference No").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">'.$rs["reference_no"].'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("PRICE").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">$ '.number_format($rs["price"],2).'/'.$tr->translate("Day").'</span></div><div class="clearfix"></div></li>
									<li><div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;"><span class="span_title">'.$tr->translate("Extra Price").'</span></div> <div class="col-md-6 col-sm-6 col-xs-12" style="  margin: 0; padding: 0;">: <span class="span_value">$ '.number_format($rs["extra_price"],2).'/'.$tr->translate("Day").'</span></div><div class="clearfix"></div></li>
								</ul>
							</div>
						</div>
						<div class="col-xs-12 bottom text-center">
							<div class="col-xs-12 col-sm-12 emphasis">
								<ul class="list-unstyled">
									<li>
										<div class="col-md-6 col-sm-6 col-xs-12"><span class="span_title"><input type="checkbox" name="equipment_'.$k.'" class="checkbox input-checkbox" onClick="addEquipment('.$k.')" id="equipment_'.$k.'" style="display: inline-block;" /> '.$tr->translate("Number of Rent").'</span></div>
										<div class="col-md-6 col-sm-6 col-xs-12"><input onKeyup="calculateGrandtotal();" type="text" class="form-control" name="number_equipment_'.$k.'"  id="number_equipment_'.$k.'" placeholder="'.$tr->translate("Qantity").'"></div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				';
			}
			$productlist.='<input type="hidden" name="identity_equipment" id="identity_equipment"/>';
			$array = array(
					'vehilce_available'=>$carlist,
					'guide_available'=>$guidlist,
					'product_available'=>$productlist
			);
		
			return $array;
		}
}
?>