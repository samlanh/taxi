<?php

class Booking_Model_DbTable_DbBooking extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_branch';
    
    function getIdNamecustomer(){
    	$sql="SELECT id,customer_code,first_name,last_name FROM ldc_customer WHERE 1";
    	return $this->getAdapter()->fetchAll($sql);
    }
    function getNameCustomer($client_id){
    	$sql="SELECT c.id,c.`customer_code`,c.`first_name`,c.`last_name`,c.`email`,c.`phone`,c.`sex` FROM ldc_customer AS c WHERE id=".$client_id;
    	$order=' LIMIT 1';
    	return $this->getAdapter()->fetchRow($sql.$order);
    }
    function getVehiclerefNo(){
    	$sql="SELECT id,reffer FROM ldc_vehicle WHERE 1";
    	$order=' ORDER BY id DESC';
    	return $this->getAdapter()->fetchAll($sql.$order);
    }
    function getVehiclePrice($data){
    	$sql="SELECT
				    v.price
					FROM ldc_vehiclefee_detail AS v
					WHERE v.packageday_id = (SELECT
                           r.id
                         FROM ldc_rankday AS r
                         WHERE r.from_amountday <= $data
                             AND r.to_amountday >= $data)";
    	$row = $this->getAdapter()->fetchOne($sql);
    	if($row!=null or $row!=""){
    		return $row;
    	}else{
    		$sql="SELECT
    		v.price
    		FROM ldc_vehiclefee_detail AS v
    		WHERE v.packageday_id = (SELECT
    		r.id
    		FROM ldc_rankday AS r
    		WHERE r.is_morethen !=0 )";
    		return $this->getAdapter()->fetchOne($sql);
    	}
    }
    
    function createSessionCarRental($data,$step=1){
    	// 		$this->clearSessionBYStep($step);
    	$session =new Zend_Session_Namespace('carRentalbooking');
    	$db_globle = new Application_Model_DbTable_DbGlobal();
    	if($step==1){
    		$pickup_date = date_create($data['pickup_date']);
    		$return_date = date_create($data['return_date']);
    		$diff=date_diff($pickup_date,$return_date);
    		$total_day = $diff->format("%a%")+1;
    			
    		$session->point_step=1;
    		$session->step1=1;
    		$session->step2 = 0;
    		$session->step3 = 0;
    		$session->step4 = 0;
    		$session->step5 = 0;
    		$session->step6 = 0;
    		$session->pickup_date = $data['pickup_date'];
    		$session->pickup_time = $data['pickup_time'].":".$data['pickup_minute'];
    		$session->return_date = $data['return_date'];
    		$session->return_time = $data['return_time'].":".$data['return_minute'];
    			
    		$session->pickup_location = $this->getLocationById($data['pickup_location']);
    		$session->return_location = $this->getLocationById($data['return_location']);
    			
    			
    		$array= array(
    				'pickup_date'=>$data['pickup_date'],
    				'return_date'=>$data['return_date'],
    				'return_time'=>$data['return_time'].":".$data['return_mins'],
    				'is_month'	 =>$data["rental_type"],
    		);
    		$session->rowsguide = $this->getAllAvailableGuide($array);
    		$session->vehiclevaliable = $db_globle->getAllAvailableVehicle($array);
    		$session-> productAvailable= $db_globle->getEquipment($array);
    	}
    	elseif($step==2){
    		
    		$pickup_date = date_create($session->pickup_date);
    		$return_date = date_create($session->return_date);
    		$diff=date_diff($pickup_date,$return_date);
    		$total_day = $diff->format("%a%")+1;
    			
    		$session->point_step=2;
    		$session->step2 =1;
    		$session->step3 = 0;
    		$session->step4 = 0;
    		$session->step5 = 0;
    		$session->step6 = 0;
    		$vehicle = $this->getVehicleSelected($data);
    		$discount = $this->getVehicleDiscount($data);
    		// 			print_r($discount);exit();
    		$array=array(
    				'id'				=>  $vehicle["id"],
    				'refun_deposit'		=>	$vehicle["refun_deposit"],
    				'vehicle_name'		=>	$vehicle["make"]." ".$vehicle["model"]." ".$vehicle["sub_model"]." ".$vehicle["reffer"],
    				'discount'			=>	$discount["discount"],
    		);
    		$session->vehicle=$array;
    		$session->vehiclePrice = $db_globle->getVehiclePrice($total_day, $data);
    		$pickup_price =$this->getPickUpPrice($data);
    		$session->pickup_price = $pickup_price;
    	}elseif($step==3){
    		$session->point_step=3;
    		$session->step3 =1;
    		$session->step4 = 0;
    		$session->step5 = 0;
    		$session->step6 = 0;
    		$driver = $this->getDriverSelected($data);
    		$session->driver=$driver;
    		//$session->step4 = 0;
    	}elseif($step==4){
    		$session->point_step=4;
    		$session->step4 =1;
    		$session->step5 = 0;
    		$session->step6 = 0;
    		$products = $this->getProductSelected($data);
    		$session->products=$products;
    		//$session->step4 = 0;
    	}elseif($step==5){
    		$session->point_step=5;
    		$session->step5 =1;
    		$session->step6 = 0;
			if($data["identity_other_fee"]!=""){
				$ids = explode(',', $data['identity_other_fee']);
				foreach ($ids as $i){
					$arr[$i] = array(
						'item_name'	=>	$data["other_fee_note".$i],
						'price'		=>	$data["other_fee".$i]
					);
				}
			}
    		
    		$session->other_fee = $arr;
    			
    	}elseif($step==6){
    		$session->point_step=6;
    		$session->step6 = 1;
    		$array = array(
    				'customer_id'		=>	$data["cu_id"],
    				'fly_no'			=>	$data["fly_no"],
    				'fly_date'			=>	$data["fly_date"],
    				'fly_time'			=>	$data["fly_time"],
    				'fly_destination'	=>	$data["fly_destination"],
    		);
    		$session->fly_info = $array;
    			
    	}elseif($step==7){
    		$session->point_step=7;
    		$session->step7 =1;
    	}
    	return true;
    }
    
    function getLocationById($id){
    	$db = $this->getAdapter();
    	$db_gb = new Application_Model_DbTable_DbGlobal();
    	$lang= $db_gb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$sql="SELECT p.`id`,".$array[$lang]." as province_name FROM `ldc_province` AS p WHERE p.`id`=$id";
    	return $db->fetchRow($sql);
    }
    
    function getPickUpPrice($data){
    	$db = $this->getAdapter();
    
    	$session =new Zend_Session_Namespace('carRentalbooking');
    	$pickup = $session->pickup_location;
    	$return = $session->return_location;
    	$pickup_id = $pickup["id"];
    	$return_id = $return["id"];
    
    	$sql="SELECT p.`id`,p.`price`,p.`tax` FROM `ldc_pickupcarprice` AS p WHERE p.`form_location`=$pickup_id AND p.`to_location`=$return_id AND p.`vehicle_id`=$data";
    	return $db->fetchRow($sql);
    }
    function getVehicleDiscount($id){
    	$db = $this->getAdapter();
    	$date = date("Y-m-d");
    	$sql="SELECT v.discount FROM `ldc_vehicle` AS v WHERE '$date' BETWEEN v.`discount_fromdate` AND v.`discount_todate` AND v.`id`=$id";
    	// 		echo $sql;exit();
    	//print_r($db->fetchOne($sql));exit();
    	return $db->fetchRow($sql);
    }
    
    function getProductSelected($data){
    	if(!empty($data['identity_equipment'])){
    		//print_r($data);exit();
    		$ids = explode(',', $data['identity_equipment']);
    		if(!empty($ids))foreach ($ids as $i){
    
    			$product_id = $this->getProductNameById($data['equipmentid_'.$i]);
    			$product_price = $this->getProductPrice($data['equipmentid_'.$i]);
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
    function getProductPrice($pro_id){
    	$db = $this->getAdapter();
    	$session =new Zend_Session_Namespace('carRentalbooking');
    	$pickup_date = date_create($session->pickup_date);
    	$return_date = date_create($session->return_date);
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
    
    function getVehicleSelected($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT v.`id`,v.ordering AS refun_deposit,(SELECT `title` FROM `ldc_make` WHERE `id`=v.`make_id`) AS make ,(SELECT `title` FROM `ldc_model` WHERE `id`=v.`model_id`) AS model,(SELECT `title` FROM `ldc_submodel` WHERE `id`=v.`sub_model`) AS sub_model ,v.`reffer` FROM `ldc_vehicle` AS v WHERE v.id=$id";
    	$row = $db->fetchRow($sql);
    	return $row;
    }
    static function getProductRankingDayPrice($product_id){
    	$sql = "
    	SELECT id,(SELECT CONCAT(from_amountday,'-',to_amountday,' Days') FROM `ldc_rankday` WHERE id=package_id) AS package_name,
    	(SELECT equipment_name FROM `ldc_stuff` WHERE id=stuff_id) AS equipment_name,price
    	FROM `ldc_stuff_details` WHERE status=1 AND stuff_id = $product_id ";
    	$db = new Application_Model_DbTable_Dbstuffrental();
    	return $db->getGlobalDb($sql);
    }
    function getStuffNameById($id){
    	$db = $this->getAdapter();
    	$sql = " SELECT equipment_name FROM `ldc_stuff` WHERE id=$id AND status=1 ";
    	return $db->fetchOne($sql);
    }
    public function getAllAvailableGuide($data,$type=3){ // 1=driver,2=guide,3=both
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
    	$sql="SELECT d.`id`,d.`driver_id`,CONCAT(d.`first_name`,' ',d.`last_name`) AS `name`,d.`experience_desc`,d.`sex`,d.`nationality`,d.`lang_note`,d.`tel`,d.`email`,d.`photo`,d.`c_holidayprice`,d.`c_normalprice`,d.`c_otprice`,d.`c_weekendprice`,d.`p_holidayprice`,d.`p_normalprice`,d.`p_otprice`,d.`p_weekendprice`,d.`monthly_price`,d.`position_type`
    	FROM `ldc_driver` AS d WHERE  d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND b.`return_date` BETWEEN '$pickupdate' AND '$returndate'  AND bd.item_type=2 AND b.status !=3) AND d.`status`=1 $position_type"; // Will Include with new Version AND b.`return_time` >= '$returntime'
    	//return $sql;exit();
    	return $db->fetchAll($sql);
    }
    
    public function addProductRental($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$session_user=new Zend_Session_Namespace('authcar');
    	$user_id = $session_user->user_id;
    	try{
    		$session =new Zend_Session_Namespace('carRentalbooking');
    
    		$db_globle = new Application_Model_DbTable_DbGlobal();
    		$booking_code = $db_globle->getNewBookingCode();
    
    		$customer_info = $session->fly_info;
    
    		$pickup_time= $session->pickup_time;
    		$return_time = $session->return_time;
    
    		$pickup_date = new DateTime($session->pickup_date);
    		$return_date = new DateTime($session->return_date);
    
    		$diff=date_diff(date_create($session->pickup_date),date_create($session->return_date));
    		$total_day = $diff->format("%a%")+1;
    
    		$row_vehicle = $session->vehicle;
    		$row_vehicle_price = $session->vehiclePrice;
    		$rows = $session->products;
    		$row_driver = $session->driver;
    		$pickup_location = $session->pickup_location;
    		$return_location = $session->return_location;
    		$row_pickup = $session->pickup_price;
    
    		//Vehicle Blog
    		$total_price_vehicle = number_format((($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$row_vehicle["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100),2);
    		$total_deposit_vehicle = $row_vehicle["refun_deposit"];
    		$vat_vehicle = $row_vehicle_price["vat_value"];
    
    		//Product blog
    		$total_price_product =0;
    		if(!empty($rows)){
	    		foreach ($rows as $row){
	    			$total_price_product+= number_format(($row['pro_price']*$row['amount_rent'])*$total_day,2);
	    		}
    		}
    		// Driver Blogml/test/application/modules/booking/models/DbTable/DbBooking.php
    		$total_price_driver = 0;
    		if(!empty($row_driver)){
	    		foreach ($row_driver as $row){
	    			$total_price_driver+= number_format(($row['driver_price']*$row['amount_rent'])*$total_day,2);
	    			//$refun_deposit_driver += $row["refund_deposit"];
	    		}
    		}
    
            $total_other_fee = 0;
    		if($session->other_fee !=""){
    			foreach ($session->other_fee as  $rs){
    				$total_other_fee+= $rs["price"];
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
    				'customer_id'			=>	$customer_info["customer_id"],
    				'booking_no'			=>	$booking_code,
    				'date_book'			=>	date("Y-m-d"),
    				'pickup_date'			=>	$pickup_date->format('Y-m-d'),
    				'pickup_time'			=>	$pickup_time,
    				'return_date'			=>	$return_date->format('Y-m-d'),
    				'return_time'			=>	$return_time,
    				'total_fee'				=>	$total_fee,
    				'total_paymented'		=>	$total_pay,
    				'item_type'				=>	1,
    				//'rental_type'		=>	$rental_type,
    				//'total_duration'	=>	$session->duration,
    				'pickup_location'		=>	$pickup_location["id"],
    				'dropoff_location'		=>	$return_location["id"],
    				'fly_no'				=>	$customer_info["fly_no"],
    				'fly_date'				=>	$customer_info["fly_date"],
    				'fly_time_of_arrival'	=>	$customer_info["fly_time"],
    				'fly_destination'		=>	$customer_info["fly_destination"],
    				'status'				=>	1,
    				'deposite_fee'			=>	$deposit_fee,
    				'total_vat'				=>	$vat_pickup+$vat_vehicle,
                                'user_id'				=>	$user_id,
    		);
    
    		if($data["payment_type"]==1){
    			$arr['visa_name']= $data["card_name"];
    			$arr['card_id']  = $data["card_id"];
    			$arr['secu_code']= $data["secu_code"];
    			$arr['card_exp_date']=$data["card_exp_date"];
    			$arr['card_id']  = $data["card_id"];
    			$arr['payment_type']=1;
    			//$arr['status']=1;
    
    		}elseif($data["payment_type"]==2){
    			$arr['card_id']=$data["wu_code"];
    			$arr['payment_type']=2;
    			//$arr['status']=2;
    		}elseif($data["payment_type"]==3){
    			$arr['payment_type']=3;
    			//$arr['status']=2;
    		}elseif($data["payment_type"]==4){
    			$arr['payment_type']=4;
    			//$arr['status']=2;
    		}
    
    		$this->_name = "ldc_booking";
    		$book_id = $this->insert($arr);
    		
    		//Other fee blog
    		if($session->other_fee !=""){
    			foreach ($session->other_fee as  $rs){
    				$arr = array(
    						'book_id'			=>	$book_id,
    						'item_name'			=>	$rs["item_name"],
    						'price'				=>	$rs["price"],
    						'total'				=> 	$rs["price"],
    						'total_paymented'	=>	$rs["price"],
    						'item_type'			=>	7,
    						'status'			=>	1,
    				);
    				$this->_name="ldc_booking_detail";
    				$this->insert($arr);
    			}
    		}
    
    		// Vehicle info Blog
    
    		$arr_deatail = array(
    				'book_id'			=>	$book_id,
    				'item_id'			=>	$row_vehicle["id"],
    				'item_name'			=>	$row_vehicle["vehicle_name"],
    				'rent_num'			=>	1,
    				'price'				=>	$row_vehicle_price["price"],
    				'VAT'				=>	$row_vehicle_price["vat_value"],
    				'total'				=>	(($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$row_vehicle["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100),
    				'total_paymented'	=>	(($row_vehicle_price["price"]*$total_day)-(($row_vehicle_price["price"]*$total_day)*$row_vehicle["discount"]/100))+($row_vehicle_price["price"]*$row_vehicle_price["vat_value"]/100),
    				'status'			=>	1,
                    'refund_deposit'	        =>	$row_vehicle["refun_deposit"],
    				'discount'			=>	$row_vehicle["discount"],
    				'item_type'			=>	1
    		);
    
    		$this->_name="ldc_booking_detail";
    		$this->insert($arr_deatail);
    		
    
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
    				//print_r($row);exit();
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
    
}  
	  

