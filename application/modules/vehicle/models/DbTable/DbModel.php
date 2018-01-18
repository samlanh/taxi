<?php

class Vehicle_Model_DbTable_DbModel extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_model';
    function addModel($data){
    	$_arr = array(
    			'brand_id'=>$data['make'],
    			'title'=>$data['model'],
    			'status'=>$data['status'],
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateModel($data){
    	$_arr = array(
    			'brand_id'=>$data['make'],
    			'title'=>$data['model'],
    			'status'=>$data['status'],
    	);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    	
   function getAllModel($search=null){
    	$db = $this->getAdapter();
    	$sql='SELECT id,title,(SELECT title FROM ldc_make WHERE id=m.brand_id ) AS brand_id,`status` 
    	      FROM ldc_model AS m WHERE status>-1';
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$order);
    }
    
 function getModelById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,brand_id,title,status FROM $this->_name WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
    
    function getAllModelById($model_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title AS name FROM $this->_name WHERE brand_id=".$model_id;
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$order);
    }
    
}  
	  

