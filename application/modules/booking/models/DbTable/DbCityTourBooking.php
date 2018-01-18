 <?php

class Booking_Model_DbTable_DbCityTourBooking extends Zend_Db_Table_Abstract
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
    
    function createSessionBookingCityTour($data,$step=1){
    	// 		$this->clearSessionBYStep($step);
    	$session =new Zend_Session_Namespace('cityTourbooking');
    	$db_globle = new Application_Model_DbTable_DbGlobal();
    	if($step==1){
    		$session->package_id=$data;
    		$session->package_name=$this->getPackageCityTourById($data);
    		$session->max_hour=$this->getMaxHourByPackageId($data);//to show max hour in front
    		$session->step1=1;
    		$session->step2=0;
    		$session->step3=0;
    		$session->step4=0;
    		$session->point_step=1;
    	}elseif($step==2){
    		$session->point_step=2;
    		$session->step2 =1;
			$data['return_date']=$data['pickup_date'];
			$session->pickup_date = empty($data['pickup_date'])? null : $data['pickup_date'];
			$session->pickup_time = empty($data['pickup_time'])? null : $data['pickup_time'];
			$session->pickup_mins = empty($data['pickup_minute'])? null : $data['pickup_minute'];
			
			$session->return_date = empty($data['return_date'])? null : $data['return_date'];
			$session->return_time =empty($data['return_time'])? "17" : $data['return_time'];
			$session->return_mins =empty($data['return_minute'])? "30" : $data['return_minute'];
			$db = new Application_Model_DbTable_DbGlobal();
			$session->vehiclevaliable = $db->getAllAvailableVehicle($data);
    	}
    	elseif($step==3){
    		
    		$discount = $this->getVehicleDiscount($data);
    		$session->point_step=3;
			$session->step3 =1;
			$session->vehicle_id=$data;//$data get parram store value only not array
			$session->price=$this->getPriceBYPackageandVehicle($data,$session->package_id);
			$db = new Application_Model_DbTable_DbGlobal();
			$array= array(
					'pickup_date'=>$session->pickup_date,
					'return_date'=>$session->pickup_date,
					//'return_time'=>$session->pickup_time.":".$session->pickup_mins,
			);
			$session->guideavaliable =$db->getAvailableDriver($array);
			$row = $db->geVehicleById($data);
			$session->vehicle_name =$row["make"]." ".$row["model"]." ".$row["sub_model"];
			$session->reff =$row["reffer"];
    	}elseif($step==4){
    		$session->point_step=4;
    		$session->step4 =1;
    		$driver = $this->getDriverSelected($data);
    		$session->driver=$driver;
    		$session->step5 = 0;
    	}elseif($step==5){
    		$session->point_step=5;
    		$session->step5 =1;
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
    		$session->step6 =1;
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
	    	$dbgb = new Application_Model_DbTable_DbGlobal();
	    	$lang= $dbgb->getCurrentLang();
	    	$array = array(1=>"province_en_name",2=>"province_kh_name");
	    	$sql="SELECT p.`id`,".$array[$lang]." as province_name FROM `ldc_province` AS p WHERE p.`id`=$id";
	    	return $db->fetchRow($sql);
	    }
    
    function getPickUpPrice($data){
    	$db = $this->getAdapter();
    
    	$session =new Zend_Session_Namespace('cityTourbooking');
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
    	$session =new Zend_Session_Namespace('cityTourbooking');
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
    	FROM `ldc_driver` AS d WHERE  d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND '$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date` AND '$returndate ' BETWEEN b.`pickup_date` AND b.`return_date` AND (bd.item_type=2 OR bd.item_type=4 OR bd.item_type=5) AND b.status !=3) AND d.`status`=1 $position_type"; // Will Include with new Version AND b.`return_time` >= '$returntime'
    	//return $sql;exit();
    	return $db->fetchAll($sql);
    }
    
    public function addProductRental($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$session_user=new Zend_Session_Namespace('authcar');
    	$user_id = $session_user->user_id;
    	try{
    		$session =new Zend_Session_Namespace('cityTourbooking');
    
    		$db_globle = new Application_Model_DbTable_DbGlobal();
    		$booking_code = $db_globle->getNewBookingCode();
    
    		$customer_info = $session->fly_info;
    
    		$pickup_time= ($session->pickup_time).":".$session->pickup_mins;
    		$return_time = ($session->return_time).":".$session->return_mins;
    
    		$pickup_date = new DateTime($session->pickup_date);
    		$return_date = new DateTime($session->return_date);
    
    		$row_driver = $session->driver;
    
    		//Vehicle Blog
    		$total_price_vehicle = number_format($session->price,2);
    		//$vat_vehicle = $row_vehicle_price["vat_value"];
    
    		// Driver Blog
    		$total_price_driver = 0;
    		if(!empty($row_driver)){
	    		foreach ($row_driver as $row){
	    			$total_price_driver+= number_format(($row['driver_price']*$row['amount_rent'])*1,2);
	    			//$refun_deposit_driver += $row["refund_deposit"];
	    		}
    		}
                $total_other_fee = 0;
    		if($session->other_fee !=""){
     			foreach ($session->other_fee as  $rs){
     				$total_other_fee+= $rs["price"];
     			}
    		 }
     		if($data["payment_type"]==4){
    			$total_fee = $total_price_driver+$total_price_vehicle+$total_other_fee;
                       //$deposit_fee = 0;
     			$total_pay = $data["cash_pay"];
    		}else{
    			$total_fee = $total_price_driver+$total_price_vehicle+$total_other_fee;
//     			$deposit_fee = $diposit;
    			$total_pay = $total_price_driver+$total_price_vehicle+$total_other_fee;
    		}
    		//Pickup & Return Blog
    
    		$arr = array(
    				'customer_id'			=>	$customer_info["customer_id"],
    				'booking_no'			=>	$booking_code,
    				'package_id'			=>	$session->package_id,
    				'date_book'			=>	date('Y-m-d'),
    				'pickup_date'			=>	$pickup_date->format('Y-m-d'),
    				'pickup_time'			=>	$pickup_time,
    				'return_date'			=>	$return_date->format('Y-m-d'),
    				'return_time'			=>	$return_time,
    				'total_fee'			=>	$total_fee,
    				'total_paymented'		=>	$total_pay,
    				'item_type'			=>	3,
    				//'rental_type'		        =>	$rental_type,
    				//'total_duration'	        =>	$session->duration,
    				'fly_no'			=>	$customer_info["fly_no"],
    				'fly_date'			=>	$customer_info["fly_date"],
    				'fly_time_of_arrival'	        =>	$customer_info["fly_time"],
    				'fly_destination'		=>	$customer_info["fly_destination"],
    				'status'			=>	1,
//     				'total_vat'			=>	$vat_pickup+$vat_vehicle,
    				'user_id'			=>	$user_id,
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
    		// Vehicle info Blog
    
    		$arr_deatail = array(
    				'book_id'			=>	$book_id,
    				'item_id'			=>	$session->vehicle_id,
    				'item_name'			=>	($session->vehicle_name)." ".$session->reff,
    				'rent_num'			=>	1,
    				'price'				=>	$session->price,
    				//'VAT'				=>	$row_vehicle_price["vat_value"],
    				'total'				=>	$session->price,
    				'total_paymented'	=>	$session->price,
    				'status'			=>	1,
    				//'discount'			=>	$row_vehicle["discount"],
    				'item_type'			=>	1
    		);
    
    		$this->_name="ldc_booking_detail";
    		$this->insert($arr_deatail);
    		//Other Fee Blog
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
    		// Driver Info Blog
    		if(!empty($row_driver)){
    			foreach ($row_driver as $row){
    				$total_price = number_format(($row['driver_price']*$row['amount_rent'])*1,2);
    				$arr_driver = array(
    						'book_id'	=>	$book_id,
    						'item_id'	=>	$row["driver_id"],
    						'item_name'	=>	$row["driver_name"],
    						'rent_num'	=>	$row["amount_rent"],
    						'price'		=>	$row['driver_price'],
    						'total'		=>	$total_price,
    						'VAT'		=>	0,
    						'total_paymented'	=>	$total_price,
    						//'refund_deposit'	=>	$refun_deposit_driver,
    						'status'	=>	1,
    						'item_type'	=>	2
    				);
    					
    				$this->_name="ldc_booking_detail";
    				$this->insert($arr_driver);
    			}
    		}
    		$db->commit();
    		return $book_id;
    	}catch (Exception$e){
    		$db->rollBack();
    		echo $e->getMessage();
                Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }
    public function getLocationByProId($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT p.`id`,p.`location_name`,(SELECT pl.pic_title FROM ldc_picture_location AS pl WHERE pl.`location_id`=p.`id` LIMIT 1) AS pic_title FROM `ldc_package_location` AS p WHERE p.`province_id`=$id AND service_type=1";
    	return $db->fetchAll($sql);
    }
    function getPackageCityTourById($package_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT location_name FROM `ldc_package_location` WHERE id=$package_id AND service_type=1 ";
    	return $db->fetchOne($sql);
    }
    function getPriceBYPackageandVehicle($vehicle_id,$package_id){
    	$db = $this->getAdapter();
    	$sql="SELECT price FROM `ldc_vehicletaxitour` WHERE vehicle_id=$vehicle_id AND package_id=$package_id LIMIT 1";
    	return $db->fetchOne($sql);
    }
    function getMaxHourByPackageId($package_id){
    	$db= $this->getAdapter();
    	$sql =" SELECT max_hour FROM `ldc_vehicletaxitour` WHERE package_id=$package_id LIMIT 1 ";
    	return $db->fetchOne($sql);
    }
    
    function getAllCityTourBooking($search){
    	$db = $this->getAdapter();
    	$from_date=$search["from_book_date"];
    	$to_date=$search["to_book_date"];
    	$sql = "SELECT b.`id`,b.`booking_no`,(SELECT CONCAT(c.`first_name`,' ',c.`last_name`) FROM `ldc_customer` AS c WHERE c.`id`=b.`customer_id`) AS customer,b.`date_book`,CONCAT(b.`pickup_date`,' ',b.`pickup_time`) AS pickup_date,CONCAT(b.`return_date`,' ',b.`return_time`) AS return_date,b.`total_fee`,b.`total_paymented` FROM `ldc_booking` AS b,`ldc_customer` AS c WHERE b.`item_type`=3 AND c.`id`=b.`customer_id` AND b.`date_book`>='$from_date' AND b.`date_book`<='$to_date'";
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
    	$order=' ORDER BY id DESC';
    	//echo $sql.$where.$order;
    	return $db->fetchAll($sql.$where.$order);
    }
}  
	  

