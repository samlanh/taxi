<?php

class Driverguide_Model_DbTable_DbTaxiprice extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehicletaxi';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    }
    function addVehicletaxi($data){
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
	    	 	 	'vehicle_id'=>$data['reference_id'],
    				'tax'=>$data['tax'],
    				'status'=>$data['status'],
	    	 		'from_location'=>$data['from_locaation'.$i],
		    		'to_location'=>$data['to_locaation'.$i],
		    		'distance'=>$data['distant_'.$i],
		    		'rate'=>$data['rate_'.$i],
    				'price'=>$data['price_'.$i],
    				'discount_trip'=>$data['discount_'.$i],
    				'round_trip'=>$data['round_trip_'.$i],
    				'note'=>$data['note_'.$i],
		    		'date'=>date("Y-m-d"),
		    		'user_id'=> $this->getUserId()
	    	        );
	    	$this->insert($item);
    	}
    }
    function updateVehicletaxi($data){
    	$db = $this->getAdapter();
    	$where='vehicle_id='.$data['id'];
    	$this->delete($where);
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
    				'vehicle_id'=>$data['reference_id'],
    				'tax'=>$data['tax'],
    				'status'=>$data['status'],
    				'from_location'=>$data['from_locaation'.$i],
    				'to_location'=>$data['to_locaation'.$i],
    				'distance'=>$data['distant_'.$i],
    				'rate'=>$data['rate_'.$i],
    				'price'=>$data['price_'.$i],
    				'discount_trip'=>$data['discount_'.$i],
    				'round_trip'=>$data['round_trip_'.$i],
    				'note'=>$data['note_'.$i],
    				'date'=>date("Y-m-d"),
    				'user_id'=> $this->getUserId()
    		);
    		$this->insert($item);
    	}
    }
    function getAllVehicleTaxi(){
    	$sql="SELECT vehicle_id As id,(SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) AS vehicle_id,
    	      (SELECT location_name FROM ldc_package_location WHERE id=from_location ) AS from_location,
    	      (SELECT location_name FROM ldc_package_location WHERE id=to_location ) AS to_location,distance,rate,
              price,discount_trip,round_trip,(SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,`status` 
    	      FROM ldc_vehicletaxi WHERE `status`=1 AND from_location!=0 AND to_location!=0 ";
    	$order=' ORDER BY id DESC';
    	return $this->getAdapter()->fetchAll($sql.$order);
    }
    function getVehicleTaxiById($id){
    	$sql="SELECT id,vehicle_id,from_location,to_location,distance,rate,price,
    	      discount_trip,round_trip,tax,note,status FROM ldc_vehicletaxi WHERE vehicle_id=$id";
    	$db=$this->getAdapter();
    	return $db->fetchAll($sql);
    }
}  
	  

