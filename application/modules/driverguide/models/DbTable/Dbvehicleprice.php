<?php

class Driverguide_Model_DbTable_Dbvehicleprice extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehiclefee_detail';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    
    }
    function getAllVehiclePrice($search=null){
    	$db=$this->getAdapter();
    	$where =" ";
    	$sql="SELECT v.id,v.reffer,v.frame_no,v.licence_plate,v.`year`,
    	(SELECT title FROM ldc_make WHERE id=v.make_id) AS make_id,
    	(SELECT title FROM ldc_model WHERE id=v.model_id) AS model_id,
    	(SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
    	(SELECT `type` FROM ldc_type AS t WHERE t.id=v.type) AS `type`,
    	d.vat_value,d.date,
    	d.`status`
    	FROM ldc_vehicle AS v ,ldc_vehiclefee_detail AS d WHERE v.id=d.vehicle_id ";
    	if($search['search_status']>-1){
    		$where.= " AND v.status = ".$search['search_status'];
    	}
    	if($search['type']>-1){
    		$where.= " AND v.type = ".$search['type'];
    	}
    	if($search['make']>0){
    		$where.=" AND v.make_id = ".$search['make'];
    		echo 333;
    	}
    	if($search['model']>0){
    		$where.=" AND v.model_id = ".$search['model'];
    	}
    	if($search['submodel']>0){
    		$where.=" AND v.sub_model = ".$search['submodel'];
    	}
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=$search['adv_search'];
    		$s_where[]= " v.reffer LIKE '%{$s_search}%'";
    		$s_where[]=" v.year LIKE '%{$s_search}%'";
    		$s_where[]= " v.chassis_no LIKE '%{$s_search}%'";
    		$s_where[]= " v.frame_no LIKE '%{$s_search}%'";
    		$s_where[]= " v.licence_plate LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	$order = " GROUP BY v.id ORDER BY v.id DESC";
//     	echo $sql.$where.$order;
    	return $db->fetchAll($sql.$where.$order);
    }
    function addVehicleRental($_data){
    	$ids = explode(',', $_data['record_row']);
    	foreach ($ids as $i){
    		$arr = array(
    				'vehicle_id'=>$_data['reference_id'],
    				'vat_value'=>$_data['tax'],
    				'status'=>$_data['status'],
    				'packageday_id'=>$_data['package_id'.$i],
    				'price'=>$_data['price_'.$i],
    				'extraprice'=>$_data['extracharge_'.$i],
    				'note'=>$_data['note_'.$i],
    				'user_id'=>$this->getUserId(),
    				'date'=>date("Y-m-d")
    		);
    		$this->insert($arr);
    	}
    }
    function updateVehicleRental($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    	$id = $_data['reference_id'];
    	$where = " vehicle_id = ".$_data['reference_id'];
    	$arr = array('status'=>0);
    	$this->delete($where);
    	
    	$ids = explode(',', $_data['record_row']);
	    	foreach ($ids as $i){
	    		$arr = array(
	    				'vehicle_id'=>$_data['reference_id'],
	    				'vat_value'=>$_data['tax'],
	    				'status'=>$_data['status'],
	    				'packageday_id'=>$_data['package_id'.$i],
	    				'price'=>$_data['price_'.$i],
	    				'extraprice'=>$_data['extracharge_'.$i],
	    				'note'=>$_data['note_'.$i],
	    				'user_id'=>$this->getUserId(),
	    				'date'=>date("Y-m-d")
	    		);
	    		$this->insert($arr);
	    	}
	    	$db->commit();
    	}catch( Exception $e){
    		$db->rollBack();
    	}
    	 
    }
    public function updateDriver($_data){
    	
    	$this->update($_arr, $where);
    }
    public function getVehicleById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT v.reffer,v.frame_no,v.licence_plate,
               (SELECT title FROM ldc_make WHERE id=v.make_id) AS make_id,
		       (SELECT title FROM ldc_model WHERE id=v.model_id) AS model_id,
		       (SELECT title FROM ldc_submodel WHERE id=v.sub_model) AS sub_model,
		       v.year,v.seat_amount 
    	FROM `ldc_vehicle` AS v WHERE v.status=1 AND v.reffer!='' AND v.id = $id LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
    public function getVehiclePriceById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name WHERE status=1 AND vehicle_id = $id ORDER BY id ASC ";
    	return $db->fetchAll($sql);
    }
   function getVehecleName(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,reffer,frame_no FROM `ldc_vehicle` WHERE STATUS=1 AND reffer!=''";
   		return $db->fetchAll($sql);
    }
    function getAllType(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,`type` FROM ldc_type WHERE `status`= 1 ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
}  
	  

