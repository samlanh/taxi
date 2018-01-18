<?php

class Driverguide_Model_DbTable_DbTaxiTour extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehicletaxitour';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    }
    function addVehicleTaxiTour($data){
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
	    	 	 	'vehicle_id'=>$data['reference_id'],
    				'frame_id'=>$data['frame_id'],
    				'tax'=>$data['tax'],
	    	 		'package_id'=>$data['package_id'.$i],
		    		'price'=>$data['price_'.$i],
		    		'one_hour'=>$data['extracharge_'.$i],
		    		'max_hour'=>$data['max_hour_'.$i],
    				'note'=>$data['note_'.$i],
    				'status'=>$data['status'],
		    		'date'=>date("Y-m-d"),
		    		'user_id'=> $this->getUserId()
	    	        );
	    	$this->insert($item);
    	}
    }
    function updateVehicleTaxiTour($data){
    	$db = $this->getAdapter();
    	$where='vehicle_id='.$data['id'];
    	$this->delete($where);
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
	    	 	 	'vehicle_id'=>$data['reference_id'],
    				'frame_id'=>$data['frame_id'],
    				'tax'=>$data['tax'],
	    	 		'package_id'=>$data['package_id'.$i],
		    		'price'=>$data['price_'.$i],
		    		'one_hour'=>$data['extracharge_'.$i],
		    		'max_hour'=>$data['max_hour_'.$i],
    				'note'=>$data['note_'.$i],
    				'status'=>$data['status'],
		    		'date'=>date("Y-m-d"),
		    		'user_id'=> $this->getUserId()
	    	        );
	    	$this->insert($item);
    	}
    }
    function getAllVehicleTaxiTour(){
        $sql="SELECT vehicle_id As id,(SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) AS vehicle_id,
               (SELECT frame_no FROM ldc_vehicle WHERE id=frame_id) AS frame_id,
               (SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,
               (SELECT location_name FROM ldc_package_location WHERE id=package_id ) AS package_id,
               price,one_hour,max_hour,note,`status`
               FROM ldc_vehicletaxitour WHERE `status` = 1";
    	$order=' ORDER BY id DESC';
    	return $this->getAdapter()->fetchAll($sql.$order);
    }
    function getVehicleTaxiTourById($id){
    	$sql="SELECT id,vehicle_id,frame_id,tax,package_id,price,one_hour,max_hour,note,`status` 
    	      FROM ldc_vehicletaxitour WHERE vehicle_id=$id";
    	$db=$this->getAdapter();
    	return $db->fetchAll($sql);
    }
}  
	  

