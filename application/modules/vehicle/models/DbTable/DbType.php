<?php

class Vehicle_Model_DbTable_DbType extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_type';
    function addType($data){
    	$_arr = array(
    			'type'=>$data['type'],
    			'status'=>$data['status'],
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateType($data){
    	
    	$_arr = array(
    			'type'=>$data['type'],
    			'status'=>$data['status'],
    			);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    	
    function getAllType($search=null){
    	$db = $this->getAdapter();
    	$sql="SELECT id,`type`,status
    	FROM $this->_name WHERE 1";
    	$where='';
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['adv_search']));
    		$s_where[]=" type LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status']>-1){
    		$where.= " AND status= ".$search['status'];
    	}
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$where.$order);
    }
    
 	function getTypeById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,type,status FROM $this->_name  WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
	
    function addTypeAjax($data){
    	$_arr = array(
    			'type'=>$data['type_title'],
    			'status'=>1,
    	);
    	return $this->insert($_arr);//insert data
    }
}  
	  

