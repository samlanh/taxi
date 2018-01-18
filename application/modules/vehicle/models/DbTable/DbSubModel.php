<?php

class Vehicle_Model_DbTable_DbSubModel extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_submodel';
    function addSubmodel($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'make_id'=>$data['makename'],
    			'model_id'=>$data['modelid'],
    			'title'=>$data['model'],
    			'status'=>$data['status'],
    			);
    	$this->insert($_arr);//insert data
    }
    function updateSubmodel($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'make_id'=>$data['makename'],
    			'model_id'=>$data['modelid'],
    			'title'=>$data['model'],
    			'status'=>$data['status'],
    	);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    function getAllSubModel($search=null){
    	$db = $this->getAdapter();
    	$sql='SELECT id,(SELECT title FROM ldc_make WHERE id=ldc_submodel.make_id)AS make_id,
    	(SELECT title FROM ldc_model WHERE id=ldc_submodel.model_id)AS model_id,title,
    	(SELECT name_en FROM ldc_view WHERE TYPE=2 AND key_code=status) AS status
    	FROM ldc_submodel WHERE status=1 ';
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$order);
    }
    
 function getSubModelById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,make_id,model_id,title,status FROM  $this->_name WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
}  
	  

