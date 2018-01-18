<?php

class Location_Model_DbTable_DbLocationtype extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_locationtype';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    	 
    }
    public function addLocationType($_data){
    	$db = $this->getAdapter();
	    	$_arr=array(
	    			'title' => $_data['loation_type'],
	    			'status'  => $_data['status'],
	    			'date'    => date("Y-m-d"),
	    			'create_date'=> date("Y-m-d H:i"),
	    			'user_id' => $this->getUserId()
	    	);
	    	$id =  $this->insert($_arr);
    }
    public function updateLocationType($_data){
    	$db = $this->getAdapter();
    	$_arr=array(
    			'title' => $_data['loation_type'],
    			'status'  => $_data['status'],
    			'date'    => date("Y-m-d"),
    			'user_id' => $this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
	public function getLocationTypeById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$id;
		$sql.=" LIMIT 1";
		$row=$db->fetchRow($sql);
		return $row;
	}
    function getAllLocationType($search=null){
    	$db = $this->getAdapter();
//     	$dbgb = new Application_Model_DbTable_DbGlobal();
//     	$lang= $dbgb->getCurrentLang();
//     	$arrayview = array(1=>"name_en",2=>"name_kh");
    	$sql = " SELECT id,title,date,
    	$this->_name.`status`
    	FROM $this->_name
    	WHERE title!='' ";
    	$order=" order by id DESC";
    	$where = '';
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['title']);
    		$s_where[]=" title LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if($search['status']>-1){
    		$where.= " AND status = ".$db->quote($search['status']);
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
   
}

