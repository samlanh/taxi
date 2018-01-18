<?php

class Vehicle_Model_DbTable_DbTransmission extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_transmission';
    function addTransmission($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'tran_name'=>$data['transmission'],
    			'status'=>$data['status'],
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateTransmission($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'tran_name'=>$data['transmission'],
    			'status'=>$data['status'],
    			);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    	
    function getAllTransmission($search=null){
    	$db = $this->getAdapter();
    	$sql="SELECT id,`tran_name`, status
    	FROM $this->_name WHERE 1 ";
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$order);
    }
    
 function getTransmissionById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,tran_name,status FROM $this->_name  WHERE id=".$id;
   		return $db->fetchRow($sql);
    }

}  
	  

