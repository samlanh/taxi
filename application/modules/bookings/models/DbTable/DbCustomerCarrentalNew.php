<?php

class Bookings_Model_DbTable_DbCustomerCarrentalNew extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	 
	function getAllCarrental($search=null){
		$db = $this->getAdapter();
		$sql="SELECT cd.`id`,c.`rent_no`,cd.receipt_no,c.`cost_month`,c.`start_date`,cd.`rent_date`,cd.`return_date`,c.`deposit`,c.total_rent_num,
               vt.`title` AS vehicle_type, 
              (SELECT cd.rent_date FROM `ldc_carrental_detail` AS cd WHERE cd.carrental_id=c.id ORDER BY cd.carrental_id ASC LIMIT 1) AS  rent_dates, 
              (SELECT cd.return_date FROM `ldc_carrental_detail` AS cd WHERE cd.carrental_id=c.id ORDER BY cd.carrental_id ASC LIMIT 1) AS  return_dates, 
              (SELECT c.customer   FROM `ldc_carrental_customer` AS c WHERE c.`id`=c.`customer_id` LIMIT 1) AS lessee, 
              (SELECT v.reffer FROM `ldc_vehicle` AS v WHERE v.car_type=vt.`id` LIMIT 1) AS feffer,
              (SELECT v.color FROM `ldc_vehicle` AS v WHERE v.car_type=vt.`id` LIMIT 1) AS color,
              (SELECT first_name FROM rms_users WHERE rms_users.id=c.user_id LIMIT 1) AS user_name,
              c.`status`,c.`is_return`,cd.`paid`,cd.`toatal_amount_fix`,
              (SELECT cd.is_paid FROM `ldc_carrental_detail` AS cd WHERE cd.carrental_id=c.id LIMIT 1) AS is_paid
             FROM  ldc_carrental AS c,`ldc_vechicletye` AS vt,`ldc_carrental_detail` AS cd
             WHERE c.vehicle_type=vt.id
             AND c.`id`=cd.`carrental_id`";
		$where = '';
		$from_date =(empty($search['start_date']))? '1': "c.`rent_date` >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " c.`rent_date` <= '".$search['end_date']." 23:59:59'";
		$where = " AND ".$from_date." AND ".$to_date;
		
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
		if ($search['lessee_name']>0){
			$where.=" AND c.`customer_id`=".$search['lessee_name'];
		}
		
		if ($search['vehicle_type']>0){
		    $where.=" AND c.vehicle_type=".$search['vehicle_type'];
		}
		
		if ($search['plate_number']>0){
		    $where.=" AND (SELECT v.id FROM `ldc_vehicle` AS v WHERE v.car_type=vt.`id`)=".$search['plate_number'];
		}
		
		if ($search['is_return']>-1){
		    $where.=" AND c.`is_return`=".$search['is_return'];
		}
		
// 		if ($search['status']>-1){
// 		    $where.=" AND c.`status`=".$search['status'];
// 		}
		
		//$order=' ORDER BY c.id DESC';
		return $db->fetchAll($sql.$where);
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
		    $_db = new Application_Model_DbTable_DbGlobal();
		    $receipt_code = $_db->getCarrInvoiceNO();
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
		   
		   if(!empty($_data['vehicle'])){
		       $veh_data=array(
		           'car_type'	  => $_data['vehicle_type'],
		           'color'	      => $_data['color'],
		           'modify_date'  => date("Y-m-d H:i:s"),
		           'user_id'      => $this->getUserId(),
		           'status'        => 1,
		       );
		       $this->_name="ldc_vehicle";
		       $where=" id=".$_data['vehicle'];
		       $this->update($veh_data, $where);
		   }
		   
			$_db = new Application_Model_DbTable_DbGlobal();
			$rent_code = $_db->getCarrentalNO();
			$_arrbooking=array(
// 			        'customer_id'  => $cus_id,
// 			        'rent_no'	   => $rent_code,
			        'rent_date'	   => date("Y-m-d",strtotime($_data['rent_date'])),
			        'start_date'   => date("Y-m-d",strtotime($_data['validity_date'])),
			        'return_date'  => date("Y-m-d",strtotime($_data['return_date'])),
					 
// 					'vehicle_type' => $_data['vehicle_type'],
// 			        'vehicle_id'   => $_data['vehicle'],
					//'color'	   => $_data['color'],
// 					'deposit'	   => $_data['deposit'],
					'return_money' => $_data['return_money'],
// 					'cost_month'   => $_data['cost_month'],
			        'is_return_car'	=> 0,
					'modify_date'   =>date("Y-m-d H:i:s"),
					'user_id'       => $this->getUserId(),
			        'status'        => 1,
			        'is_return'	    => 0,
					
			);
			$this->_name="ldc_carrental";
			$where=" id=".$_data['id'];
			$this->update($_arrbooking,$where);
			
			$_car_detail=array(
			    'receipt_no'    => $receipt_code,
			    'carrental_id'  => $_data['id'],
			    'rent_date'	    => date("Y-m-d",strtotime($_data['rent_date'])),
			    'return_date'   => date("Y-m-d",strtotime($_data['return_date'])),
			    'time'	        => $_data['time'],
			    'fix_name'	    => $_data['fix_name'],
			    'repair_date'	=> $_data['repair_date'],
			    'payment_date'	=> $_data['payment_date'],
			    'toatal_amount_fix'=> $_data['toatal_amount_fix'],
			    'paid'	        => $_data['paid'],
			    'remark'	    => $_data['remark'],
			    'create_date'   => date("Y-m-d H:i:s"),
			    'user_id'       => $this->getUserId(),
			    'status'        => 1,
			    'is_paid'       => 0,
			    'profit'        => $_data['profit'],
			    
			);
			$this->_name="ldc_carrental_detail";
			$carr_detail_id = $this->insert($_car_detail);
			
			$row=$this->getAllPaidMonth($_data['id']);
			$num=$this->getCountNo($_data['id']);
			if(!empty($row)){
			    $total=array(
			        'total_rent_num'   => $num['id'],
			        'total_maintenance'=> $row['total_maintenance']+$_data['toatal_amount_fix'],
			        'total_payment'	   => $row['total_payment']+$_data['paid'],
			        'total_profit'	   => ($row['profit'])+($_data['profit']),
			    );
			    $this->_name="ldc_carrental";
			    $where=" id=".$_data['id'];
			    $this->update($total,$where);
			}
			
			$sql = "DELETE FROM ldc_carrental_img WHERE carr_detail_id=".$_data["id"];
			$db->query($sql);
			$part= PUBLIC_PATH.'/images/imgbong/';
			if (!file_exists($part)) {
			    mkdir($part, 0777, true);
			}
			$photoname = str_replace(" ", "_", $_data['rent_no']);
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
			            'status'         =>1,
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
	
	public function updateCarrental($_data){
	    $db = $this->getAdapter();
	    $db->beginTransaction();
	    try{
	        $rows=$this->getAllCarentDetailByid($_data['id']);
	        if(!empty($rows)){
	            $num=$this->getCountNo($rows['id']);
	            $totals=array(
	                'total_rent_num'   => $num['id']-1,
	                'total_maintenance'=> abs($rows['total_maintenance']-$rows['toatal_amount_fix']),
	                'total_payment'	   => abs($rows['total_payment']-$rows['paid']),
	                'total_profit'	   => abs($rows['total_profit']-$rows['profit']),
	            );
	            $this->_name="ldc_carrental";
	            $where=" id=".$rows['id'];
	            $db->getProfiler()->setEnabled(true);
	            $this->update($totals,$where);
	            Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
	            Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
	            $db->getProfiler()->setEnabled(false);
	        }
	        
	        $_arrbooking=array(
	            'rent_date'	   => date("Y-m-d",strtotime($_data['rent_date'])),
	            'start_date'   => date("Y-m-d",strtotime($_data['validity_date'])),
	            'return_date'  => date("Y-m-d",strtotime($_data['return_date'])),
	            'return_money' => $_data['return_money'],
	            'is_return_car'	=> 0,
	            'modify_date'   =>date("Y-m-d H:i:s"),
	            'user_id'       => $this->getUserId(),
	            'status'        => 1,
	            'is_return'	    => $_data['is_return'],
	        );
	        $db->getProfiler()->setEnabled(true);
	        $this->_name="ldc_carrental";
	        $where=" id=".$rows['id'];
	        $this->update($_arrbooking,$where);
	        Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
	        Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
	        $db->getProfiler()->setEnabled(false);
	        
	        $sql = "DELETE FROM ldc_carrental_detail WHERE id=".$_data["id"];
	        $db->query($sql);
	        $_car_detail=array(
	            'receipt_no'    => $_data['receipt_no'],
	            'carrental_id'  => $rows['id'],
	            'rent_date'	    => date("Y-m-d",strtotime($_data['rent_date'])),
	            'return_date'   => date("Y-m-d",strtotime($_data['return_date'])),
	            'time'	        => $_data['time'],
	            'fix_name'	    => $_data['fix_name'],
	            'repair_date'	=> $_data['repair_date'],
	            'payment_date'	=> $_data['payment_date'],
	            'toatal_amount_fix'=> $_data['toatal_amount_fix'],
	            'paid'	        => $_data['paid'],
	            'remark'	    => $_data['remark'],
	            'create_date'   => date("Y-m-d H:i:s"),
	            'user_id'       => $this->getUserId(),
	            'status'        => 1,
	            'is_paid'       => 0,
	            'profit'        => $_data['profit'],
	        );
	        $db->getProfiler()->setEnabled(true);
	        $this->_name="ldc_carrental_detail";
	        $carr_detail_id = $this->insert($_car_detail);
	        Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
	        Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
	        $db->getProfiler()->setEnabled(false);
	        
	        $row=$this->getAllPaidMonth($rows['id']);
	        $num=$this->getCountNo($_data['id']);
	        if(!empty($row)){
	            $total=array(
	                'total_rent_num'   => $num['id'],
	                'total_maintenance'=> $row['total_maintenance']+$_data['toatal_amount_fix'],
	                'total_payment'	   => $row['total_payment']+$_data['paid'],
	                'total_profit'	   => ($row['profit'])+($_data['profit']),
	            );
	            $this->_name="ldc_carrental";
	            $where=" id=".$rows['id'];
	            $this->update($total,$where);
	        }
	        
	        
	        
	        $sql = "DELETE FROM ldc_carrental_img WHERE carr_detail_id=".$_data["id"];
	        $db->query($sql);
	        
	        $part= PUBLIC_PATH.'/images/imgbong/';
	        if (!file_exists($part)) {
	            mkdir($part, 0777, true);
	        }
	        $photoname = str_replace(" ", "_", $_data['rent_no']);
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
	                    'status'         =>1,
	                    'date'           =>date("Y-m-d")
	                );
	                $db->getProfiler()->setEnabled(true);
	                $this->_name='ldc_carrental_img';
	                $this->insert($arr);
	                Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
	                Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
	                $db->getProfiler()->setEnabled(false);
	            }
	        }
	       // exit();
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
	
	function getAllPaidMonth($id){
	    $db = $this->getAdapter();
	    $sql="  SELECT COUNT(cd.id) AS id,SUM(cd.`toatal_amount_fix`) AS toatal_amount_fix,SUM(cd.`paid`) AS paid,SUM(`profit`) AS profit,c.total_maintenance,c.total_payment,c.`total_profit`
               FROM `ldc_carrental` AS c,`ldc_carrental_detail` AS cd
               WHERE c.`id`=cd.`carrental_id`
               AND cd.`carrental_id`=$id LIMIT 1";
	    return $db->fetchRow($sql);
	}
	
	function getCountNo($id){
	    $db = $this->getAdapter();
	    $sql="  SELECT COUNT(id) as id FROM `ldc_carrental_detail` WHERE carrental_id=$id LIMIT 1";
	    return $db->fetchRow($sql);
	}
	
	function getAllCarentDetailByid($id){
	    $db = $this->getAdapter();
	    $sql="  SELECT c.id,cd.id AS id_detail,cd.`toatal_amount_fix`,cd.`paid`,c.total_maintenance,c.total_payment,c.`total_profit`,c.total_rent_num,cd.profit
	    FROM `ldc_carrental` AS c,`ldc_carrental_detail` AS cd
	    WHERE c.`id`=cd.`carrental_id`
	    AND cd.`id`=$id";
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
	
	function getCarrentalById($id){
	    $db = $this->getAdapter();
	    $sql="SELECT c.*,
           cd.`rent_date` AS rent_dates,cd.`return_date` AS return_dates,cd.`time`,cd.`fix_name`,profit,
           cd.`repair_date`,cd.`payment_date`,cd.`toatal_amount_fix`,cd.`paid`,cd.`remark`
           FROM `ldc_carrental` AS c,`ldc_carrental_detail` AS cd
           WHERE c.`status` = 1 AND  c.`id`=cd.`carrental_id`  AND cd.`carrental_id`=$id LIMIT 1";
	    return $db->fetchRow($sql);
	}
	
	function getCarrentalByIddetial($id){
	    $db = $this->getAdapter();
	    $sql="SELECT cd.id as id_detail,c.*,cd.receipt_no,
	    cd.`rent_date` AS rent_dates,cd.`return_date` AS return_dates,cd.`time`,cd.`fix_name`,profit,
	    cd.`repair_date`,cd.`payment_date`,cd.`toatal_amount_fix`,cd.`paid`,cd.`remark`
	    FROM `ldc_carrental` AS c,`ldc_carrental_detail` AS cd
	    WHERE c.`status` = 1 AND  c.`id`=cd.`carrental_id`  AND cd.`id`=$id LIMIT 1";
	    return $db->fetchRow($sql);
	}
	
	function checkCar($id){
	    $db=$this->getAdapter();
	    $sql="SELECT id FROM `ldc_vehicle` WHERE `car_type`=$id LIMIT 1";
	    return $db->fetchOne($sql);
	}
	
	function getImgBongById($id){
	    $db=$this->getAdapter();
	    $sql="SELECT `pic_title`  FROM `ldc_carrental_img` WHERE `carr_detail_id`=$id";
	    return $db->fetchAll($sql);
	}
	
	function getIdDetailByCarr($id){
	    $db=$this->getAdapter();
	    $sql="SELECT id  FROM `ldc_carrental_detail` WHERE `carrental_id`=$id  ORDER BY id ASC LIMIT 1";
	    return $db->fetchOne($sql);
	}
	
	function addAVehicleType($data){
	    $veh_data=array(
	        'title'	        => $data['vehicle_name'],
	        'user_id'       => $this->getUserId(),
	        'status'        => 1,
	    );
	    $this->_name="ldc_vechicletye";
	    return  $this->insert($veh_data);
	}
	
	
	function addAVehicle($data){
	    $car_data=array(
	        'reffer'	    => $data['plate_number'],
	        'color'	        => $data['color_name'],
	        'modify_date'   => date("Y-m-d H:i:s"),
	        'user_id'       => $this->getUserId(),
	        'status'        => 1,
	    );
	    $this->_name="ldc_vehicle";
	    return  $this->insert($car_data);
	}
	
	
	function addCustomer($data){
	    $db = new Application_Model_DbTable_DbGlobal();
	    $client_code = $db->getcustomerno();
	    $_arr=array(
	        'cus_no'      => $client_code,
	        'customer'    => $data['customers'],
	        'sex'         => 1,
	        //'nationality' => $_data['nationality'],
	        'phone'	      => $data['phone_numbers'],
	        'passport'	  => $data['passports'],
	        'address'	  => $data['addresss'],
	        'create_date' => date("Y-m-d H:i:s"),
	        'modify_date' => date("Y-m-d H:i:s"),
	        'status'      => 1,
	        'user_id'	  => $this->getUserId(),
	    );
	    $this->_name="ldc_carrental_customer";
	    return  $this->insert($_arr);
	}
	
}
?>