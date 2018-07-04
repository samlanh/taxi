<?php

class Vehicle_Model_DbTable_DbVehicle extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehicle';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    function addVehicle($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    	$part= PUBLIC_PATH.'/images/vehicle/';
	    	if (!file_exists($part)) {
	    		mkdir($part, 0777, true);
	    	}
	    	$identity = $data['identity'];
	    	$ids = explode(',', $identity);
	    	$image_feature="";
	    	$image_list="";
	    	$set_image_fea =0;
	    	foreach ($ids as $i){
	    		$name = $_FILES['photo'.$i]['name'];
	    		if (!empty($name)){
	    			$ss = 	explode(".", $name);
	    			$image_name = "car_".date("Y").date("m").date("d").time().$i.".".end($ss);
	    			$tmp = $_FILES['photo'.$i]['tmp_name'];
	    			if(move_uploaded_file($tmp, $part.$image_name)){
	    				$photo = $image_name;
	    			}
	    			else
	    				$string = "Image Upload failed";
	    			if ($set_image_fea==0){
	    				$image_feature = $image_name;
	    				$set_image_fea=1;
	    			}
	    			if (empty($image_list )){
	    				$image_list=$image_name;
	    			}else{$image_list = $image_list.",".$image_name;
	    			}
	    		}
	    	}
	    	
	    	$_arr = array(
	    			'reffer'=>$data['vehicle_ref_no'],
	    			'frame_no'=>$data['frame_no'],
	    			'licence_plate'=>$data['licence_piate_no'],
	    			'max_weight'=>$data['maximum_weight'],
	    			'seat_amount'=>$data['no_of_seats'],
	    			'org_cost'=>$data['original'],
	    			'color'=>$data['color'],
	    			'date_buy'=>$data['date_buy'],
	    			'chassis_no'=>$data['chassis_no'],
	    			'make_id'=>$data['make'],
	    			'model_id'=>$data['model'],
	    			'sub_model'=>$data['submodel'],
	    			'engine'=>$data['engine'],
	    			'engine_number'=>$data['engine_number'],
	    			'transmission'=>$data['transmission'],
	    			'year'=>$data['year'],
	    			'type'=>$data['type'],
	    			'steering'=>$data['steering'],
	    			'test_url'=>$data['test_url'],
	    			
	    			'of_axlex'=>$data['of_axlex'],
	    			'of_cylinder'=>$data['of_cylinder'],
	    			'cylinders_dip'=>$data['cylinders_dip'],
	    			
	    			'show_url'=>$data['show_url'],
	    			'car_type'=>$data['vehicle_type'],
	    			'status'=>$data['status'],
	    			'discount'=>$data['discount_value'],
	    			'description'=>$data['note'],
	    			'is_promotion'=>empty($data['discount'])?0:1,
                    'discount_fromdate'=>$data['discount_fromdate'],
	    			'discount_todate'=>$data['discount_todate'],
	    			'ordering'=>$data['deposit'],
	    			'root_side_ass'=>$data['root_side_ass'],
	    			'of_axlex'=>$data['of_axlex'],
	    			'of_cylinder'=>$data['of_cylinder'],
	    			'cylinders_dip'=>$data['cylinders_dip'],
	    			
	    			'images_list'=>$image_list,
	    			'img_front'=>$image_feature,
	    			
	    			'user_id'=>$this->getUserId(),
	    			
	    			'modify_date'=>date("Y-m-d H:i:s"),
	    			'create_date'=>date("Y-m-d H:i:s"),
	    			);
	    	$id = $this->insert($_arr);
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }

//     public function getVehiclePriceById($id){ // get price Rental Vehicle By Package
//     	$this->_name='ldc_vehiclefee_detail';
//     	$db = $this->getAdapter();
//     	$sql = "SELECT * FROM $this->_name WHERE status=1 AND vehicle_id = $id ORDER BY id ASC ";
//     	return $db->fetchAll($sql);
//     }
    
    function getCarpriceById($id){  // get Price Rental Vehicle By Location to Location
    	$this->_name='ldc_pickupcarprice';
    	$sql="SELECT *
    	FROM $this->_name WHERE vehicle_id =$id";
    	$db=$this->getAdapter();
    	return $db->fetchAll($sql);
    }
    public function updateVehicle($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    	$part= PUBLIC_PATH.'/images/vehicle/';
	    	if (!file_exists($part)) {
	    		mkdir($part, 0777, true);
	    	}
	    	$identity = $data['identity'];
	    	$ids = explode(',', $identity);
	    	$image_feature="";
	    	$image_list="";
	    	$set_image_fea =0;
	    		foreach ($ids as $i){
	    			$name = $_FILES['photo'.$i]['name'];
	    			if (!empty($name)){
	    				$ss = 	explode(".", $name);
	    				$image_name = 'car_'.date("Y").time().$i.".".end($ss);
	    				$tmp = $_FILES['photo'.$i]['tmp_name'];
	    				if(move_uploaded_file($tmp, $part.$image_name)){
	    					$photo = $image_name;
	    				}
	    				else
	    					$string = "Image Upload failed";
	    			}else{
	    				$image_name = $data['old_photo'.$i];
	    			}
	    			
	    			if ($set_image_fea==0){
	    				$image_feature = $image_name;
	    				$set_image_fea=1;
	    			}
	    			if (empty($image_list )){
	    				$image_list=$image_name;
	    			}else{$image_list = $image_list.",".$image_name;
	    			}
	    		}
	    	$_arr = array(
	    			'reffer'=>$data['vehicle_ref_no'],
	    			'frame_no'=>$data['frame_no'],
	    			'licence_plate'=>$data['licence_piate_no'],
	    			'max_weight'=>$data['maximum_weight'],
	    			'seat_amount'=>$data['no_of_seats'],
	    			'org_cost'=>$data['original'],
	    			'color'=>$data['color'],
	    			'date_buy'=>$data['date_buy'],
	    			'chassis_no'=>$data['chassis_no'],
	    			'make_id'=>$data['make'],
	    			'model_id'=>$data['model'],
	    			'sub_model'=>$data['submodel'],
	    			'engine'=>$data['engine'],
	    			'engine_number'=>$data['engine_number'],
	    			'transmission'=>$data['transmission'],
	    			'year'=>$data['year'],
	    			'type'=>$data['type'],
	    			'steering'=>$data['steering'],
	    			'test_url'=>$data['test_url'],
	    			'show_url'=>$data['show_url'],
	    			'car_type'=>$data['vehicle_type'],
	    			'status'=>$data['status'],
	    			'discount'=>$data['discount_value'],
	    			'description'=>$data['note'],
	    			'is_promotion'=>empty($data['discount'])?0:1,
	                'discount_fromdate'=>$data['discount_fromdate'],
	    			'discount_todate'=>$data['discount_todate'],
	    			'ordering'=>$data['deposit'],
	    			'root_side_ass'=>$data['root_side_ass'],
	    			
	    			'of_axlex'=>$data['of_axlex'],
	    			'of_cylinder'=>$data['of_cylinder'],
	    			'cylinders_dip'=>$data['cylinders_dip'],
	    			
	    			'images_list'=>$image_list,
	    			'img_front'=>$image_feature,
	    			'user_id'=>$this->getUserId(),
	    			'modify_date'=>date("Y-m-d H:i:s"),
	    			);
	    	$where='id='.$data['id'];
	    	$this->update($_arr, $where);
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }

 	function getTypeById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,type,status FROM $this->_name  WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
    function getAllMake(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,title FROM ldc_make WHERE `status`=1 ";
    return $db->fetchAll($sql);
    }
    function getAllEnGince(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,capacity FROM ldc_engince WHERE `status`=1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllEnGinceAsname(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,capacity as name FROM ldc_engince WHERE `status`=1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllType(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,`type` FROM ldc_type WHERE `status`= 1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllTypeAsName(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,`type` as name FROM ldc_type WHERE `status`= 1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllTransmisstion(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,tran_name FROM ldc_transmission WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllVehicleType(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,title FROM ldc_vechicletye WHERE `status`= 1  AND title!=''";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllVehicleTypestore(){
        $db=$this->getAdapter();
        $sql="SELECT id,title As name FROM ldc_vechicletye WHERE `status`= 1  AND title!=''";
        return $db->fetchAll($sql);
    }
    
    function getAllPlatenNo(){
        $db=$this->getAdapter();
        $sql="SELECT v.id,v.`reffer` AS title
             FROM ldc_vechicletye AS vt,`ldc_vehicle` AS v
             WHERE v.`status`= 1   
             AND vt.`id`=v.`car_type`
             AND v.`reffer`!=''";
        $order=' GROUP BY v.car_type ORDER BY v.reffer ASC';
        return $db->fetchAll($sql.$order);
    }
    
    function getAllCustomerCarrental(){
        $db=$this->getAdapter();
        $sql=" SELECT c.id,c.customer AS `name` FROM `ldc_carrental_customer` AS c WHERE c.status=1";
        $order=' ORDER BY c.customer ASC';
        return $db->fetchAll($sql.$order);
    }
    
    function getAllVehicleTypeAsName(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,title As name FROM ldc_vechicletye WHERE `status`= 1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllSubModelById($model_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title AS name FROM ldc_submodel WHERE model_id=".$model_id;
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function addSubModelajx($data){
    		$this->_name='ldc_submodel';
    		$db = $this->getAdapter();
    		$arr = array(
    				'make_id'=>$data['txt_make_id'],
    				'model_id'=>$data['txt_model_id'],
    				'title'=>$data['txt_submodel'],
    				'status'=>1
    	
    		);
    		return $this->insert($arr);
    }
    function getAllVehicle($search=null){
    	$db=$this->getAdapter();
    	$where ="WHERE 1";
    	$sql="SELECT id,reffer,`year`,
    	       (SELECT title FROM ldc_make WHERE id=v.make_id LIMIT 1) AS make_id,
		       (SELECT title FROM ldc_model WHERE id=v.model_id LIMIT 1) AS model_id,
		       (SELECT title FROM ldc_submodel WHERE id=v.sub_model LIMIT 1) AS sub_model,
		       (SELECT `title` FROM `ldc_vechicletye` AS t WHERE t.id=v.`car_type` LIMIT 1) AS `car_type`,
                color,(SELECT capacity FROM ldc_engince WHERE id=`engine` LIMIT 1) AS `engine`,chassis_no,frame_no,licence_plate,date_buy,`status`
    	        FROM ldc_vehicle As v ";
    	//(SELECT `type` FROM ldc_type AS t WHERE t.id=v.type LIMIT 1) AS `type`,
    	if($search['search_status']>-1){
			$where.= " AND status = ".$search['search_status'];
		}
		if($search['make']>0){
			$where.=" AND make_id = ".$search['make'];
		}
		if($search['model']>0){
			$where.=" AND model_id = ".$search['model'];
		}
		if($search['submodel']>0){
			$where.=" AND sub_model = ".$search['submodel'];
		}
		if ($search['vehicle_type']>-1){
			$where.=" AND car_type = ".$search['vehicle_type'];
		}
		if ($search['year']>-1){
			$where.=" AND year = ".$search['year'];
		}
		if(!empty($search['adv_search'])){
			$s_where=array();
			$s_search=$search['adv_search'];
			$s_where[]= " reffer LIKE '%{$s_search}%'";
			$s_where[]=" year LIKE '%{$s_search}%'";
			$s_where[]= " chassis_no LIKE '%{$s_search}%'";
			$s_where[]= " frame_no LIKE '%{$s_search}%'";
			$s_where[]= " licence_plate LIKE '%{$s_search}%'";
			$s_where[]= " date_buy LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ', $s_where).')';
		}
		$order = " ORDER BY id DESC";
		//echo $sql.$where;		
		return $db->fetchAll($sql.$where.$order);	
    }
    function getVehicleById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name WHERE id=".$id." LIMIT 1";
    	return $db->fetchRow($sql);
    }
}  
	  

