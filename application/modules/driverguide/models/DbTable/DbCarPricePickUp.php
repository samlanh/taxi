<?php

class Driverguide_Model_DbTable_DbCarPricePickUp extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_pickupcarprice';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    }
    function addCarprice($data){
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
	    	 	 	'vehicle_id'=>$data['reference_id'],
    				'frame_no'=>$data['frame_id'],
    				'tax'=>$data['tax'],
	    	 		'form_location'=>$data['from_locaation'.$i],
		    		'to_location'=>$data['to_locaation'.$i],
		    		'price'=>$data['price_'.$i],
		    		'note'=>$data['note_'.$i],
    				'date'=>date("Y-m-d"),
    				'user_id'=>$this->getUserId(),
    				'status'=>$data['status'],
	    	        );
	    	$this->insert($item);
    	}
    }
    function updateCarprice($data){
    	$db = $this->getAdapter();
    	$where='vehicle_id='.$data['id'];
    	$this->delete($where);
    	$ids=explode(',', $data['record_row']);
    	foreach ($ids as $i){
    		$item =array(
	    	 	 	'vehicle_id'=>$data['reference_id'],
    				'frame_no'=>$data['frame_id'],
    				'tax'=>$data['tax'],
	    	 		'form_location'=>$data['from_locaation'.$i],
		    		'to_location'=>$data['to_locaation'.$i],
		    		'price'=>$data['price_'.$i],
		    		'note'=>$data['note_'.$i],
    				'date'=>date("Y-m-d"),
    				'user_id'=>$this->getUserId(),
    				'status'=>$data['status'],
	    	        );
    		$this->insert($item);
    	}
    }
    function getAllCarprice(){
        $sql="SELECT vehicle_id As id,(SELECT reffer FROM ldc_vehicle WHERE id=vehicle_id) AS vehicle_id,
               (SELECT frame_no FROM ldc_vehicle WHERE id=ldc_pickupcarprice.vehicle_id) As frame_no,
               (SELECT CONCAT(title,'( ',value,'%)') FROM ldc_tax WHERE value=tax) AS tax,
               (SELECT location_name FROM ldc_package_location WHERE id=form_location ) AS form_location,
               (SELECT location_name FROM ldc_package_location WHERE id=to_location ) AS to_location,price,note,`status`
                FROM ldc_pickupcarprice WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $this->getAdapter()->fetchAll($sql.$order);
    }
    function getCarpriceById($id){
    	$sql="SELECT id,vehicle_id,frame_no,tax,form_location,to_location,price,note,`status` 
    	      FROM ldc_pickupcarprice WHERE vehicle_id =$id";
    	$db=$this->getAdapter();
    	return $db->fetchAll($sql);
    }
}  
	  

