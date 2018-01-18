<?php

class Group_Model_DbTable_DbOwner extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_owner';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addOwner($_data){
    	$_arr = array(
    			'owner_name'=>$_data['owner_name'],
    			'position'=>$_data['position'],
    			'dob'=>$_data['dob'],
    			'id_card'=>$_data['id_card'],
    			'hand_phone'=>$_data['hand_phone'],
    			'email'=>$_data['email'],
    			'hotline'=>$_data['hotline'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateOwner($_data){
    	//print_r($_data);exit();
    	$_arr = array(
    			'owner_name'=>$_data['owner_name'],
    			'position'=>$_data['position'],
    			'id_card'=>$_data['id_card'],
    			'dob'=>$_data['dob'],
    			'hand_phone'=>$_data['hand_phone'],
    			'email'=>$_data['email'],
    			'hotline'=>$_data['hotline'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    	
	function getAllOwner($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,owner_name,`position`,id_card,hand_phone,email,hotline,`status` FROM ldc_owner WHERE STATUS=1";
//     	if($search['status_search']>-1){
//     		$where.= " AND b.status = ".$search['status_search'];
//     	}
//     	$where="";
    	
//     	if(!empty($search['title'])){
//     		$s_where=array();
//     		$s_search=addslashes($search['adv_search']);
//     		$s_where[]=" title LIKE '%{$s_search}%'";
//     		$s_where[]=" value LIKE '%{$s_search}%'";
//     		$s_where[]=" b.displayby LIKE '%{$s_search}%'";
//     		$where.=' AND ('.implode(' OR ',$s_where).')';
//     	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
//    		return $db->fetchAll($sql.$where.$order);
    }
    
 function getOwnerById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM ldc_owner where id= ".$id;
   		return $db->fetchRow($sql);
    }

}  
	  

