<?php

class Other_Model_DbTable_DbDay extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_rankday';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addPackageDay($_data){
    	$_arr = array(
    			'day_title'=>$_data['package_day'],
    			'from_amountday'=>$_data['from_amount'],
    			'to_amountday'=>$_data['to_amount'],
    			'is_morethen'=>empty($_data['is_morethan'])? "0" : 1,
    			'morethen'=>$_data['morethan_amount'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId(),
    			'date'=>date("Y-m-d")
    			);
    	$this->insert($_arr);//insert data
    }
    function upatePackageDay($_data){
    		$_arr = array(
    			'day_title'=>$_data['package_day'],
    			'from_amountday'=>$_data['from_amount'],
    			'to_amountday'=>$_data['to_amount'],
    			'is_morethen'=>empty($_data['is_morethan'])? "0" : 1,
    			'morethen'=>$_data['morethan_amount'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId(),
    			'date'=>date("Y-m-d")
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    function getAllPacakgeDay($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,day_title,from_amountday,to_amountday,
    	(SELECT name_en FROM `ldc_view` WHERE type=7 AND key_code =$this->_name.`is_morethen`  ) AS is_morethen
    	,morethen,date,
				$this->_name.`status`
 				FROM $this->_name WHERE day_title!='' ";
    	
//     	if($search['status_search']>-1){
//     		$where.= " AND b.status = ".$search['status_search'];
//     	}
    	$where="";
    	
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" question LIKE '%{$s_search}%'";
    		$s_where[]=" answer LIKE '%{$s_search}%'";
    		$s_where[]=" b.displayby LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
   		return $db->fetchAll($sql.$where.$order);
    }
    
 function getPackagedayById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id LIMIT 1" ;
   		return $db->fetchRow($sql.$where);
    }
}  
	  

