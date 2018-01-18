<?php

class Other_Model_DbTable_DbTax extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_tax';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addtax($_data){
    	$_arr = array(
    			'title'=>$_data['tax_title'],
    			'value'=>$_data['tax_value'],
    			'status'=>$_data['status'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateTax($_data){
    	$_arr = array(
    			'title'=>$_data['tax_title'],
    			'value'=>$_data['tax_value'],
    			'status'=>$_data['status'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    	
	function getAllTax($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title,value,
		`ldc_tax`.`status`
		AS status FROM ldc_tax where 1 ";
    	
//     	if($search['status_search']>-1){
//     		$where.= " AND b.status = ".$search['status_search'];
//     	}
    	$where="";
    	
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" title LIKE '%{$s_search}%'";
    		$s_where[]=" value LIKE '%{$s_search}%'";
    		$s_where[]=" b.displayby LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
   		return $db->fetchAll($sql.$where.$order);
    }
    
 function getTaxById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id" ;
  
   		return $db->fetchRow($sql.$where);
    }

}  
	  

