<?php

class Vehicle_Model_DbTable_DbVehicleType extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vechicletye';
    function addVehicleType($data){
    	$_arr = array(
    			'title'=>$data['vehicle_type'],
    			'status'=>$data['status'],
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateVehicleType($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'title'=>$data['vehicle_type'],
    			'status'=>$data['status'],
    			);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    	
    function getAllVehicleType($search=null){
    	$db = $this->getAdapter();
    	$sql="SELECT id,title,status
    	FROM $this->_name WHERE 1 ";
    	$where='';
  		  if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['adv_search']));
    		$s_where[]=" title LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status']>-1){
    		$where.= " AND status= ".$search['status'];
    	}
    	$order=' ORDER BY id DESC';
    	
        return $db->fetchAll($sql.$where.$order);
    }
    
 	function getVehicleTypeById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title,status FROM $this->_name  WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
    function addVehicleTypeAjax($data){
    	$_arr = array(
    			'title'=>$data['vehicletype_title'],
    			'status'=>1,
    	);
    	return $this->insert($_arr);//insert data
    }
}  
	  

