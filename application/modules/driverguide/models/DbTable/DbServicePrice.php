<?php

class Driverguide_Model_DbTable_DbServicePrice extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_serviceprice';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    
    }
   
    function addServicePrice($data){
    	$db = $this->getAdapter();
    	
//     	$adapter = new Zend_File_Transfer_Adapter_Http();
//     	$part= PUBLIC_PATH.'/images';
// 		$adapter->setDestination($part);
// 		$adapter->receive();
// 		$photo = $adapter->getFileInfo();
//     	if(!empty($photo['photo']['name'])){
//     		$img=$photo['photo']['name'];
//     	}else{
//     		$img="";
//     	}
    	
//     	if(!empty($photo['photo1']['name'])){
//     		$img1=$photo['photo1']['name'];
//     	}else{
//     		$img1="";
//     	}
    	
//     	if(!empty($photo['photo2']['name'])){
//     		$img2=$photo['photo2']['name'];
//     	}else{
//     		$img2="";
//     	}
    	
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    	$part= PUBLIC_PATH.'/images';
    	$adapter->setDestination($part);
    	$adapter->receive();
    	 
    	$photo = $adapter->getFileInfo();
    	if(!empty($photo['photo']['name'])){
    		$data['photo']=$photo['photo']['name'];
    	}else{
    		$data['photo']='';
    	}
    	if(!empty($photo['photo1']['name'])){
    		$data['photo1']=$photo['photo1']['name'];
    	}else{
    		$data['photo1']="";
    	}
    	
    	if(!empty($photo['photo2']['name'])){
    		$data['photo2']=$photo['photo2']['name'];
    	}else{
    		$data['photo2']="";
    	}
    	
    	$arr = array(
    			'service_title'=>$data['serice_title'],
    			'description'=>$data['description'],
    			'photo'=>$data['photo'],
    			'photo1'=>$data['photo1'],
    			'photo2'=>$data['photo2'],
    			'price'=>$data['price'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId(),
    			'status'=>$data['status'],
    	);
    	$this->insert($arr);
    }
    public function updateServicePrice($data){
    	$db = $this->getAdapter();
        
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    	$part= PUBLIC_PATH.'/images';
		$adapter->setDestination($part);
		$adapter->receive();
		
		$photo = $adapter->getFileInfo();
    	if(!empty($photo['photo']['name'])){
    		$img=$photo['photo']['name'];
    	}else{
    		$img=$data['oldphoto'];
    	}
    	
    	if(!empty($photo['photo1']['name'])){
    		$img1=$photo['photo1']['name'];
    	}else{
    		$img1=$data['oldphoto1'];
    	}
    	
    	if(!empty($photo['photo2']['name'])){
    		$img2=$photo['photo2']['name'];
    	}else{
    		$img2=$data['oldphoto2'];;
    	}
    	
    	$arr = array(
    			'service_title'=>$data['serice_title'],
    			'description'=>$data['description'],
    			'photo'=>$img,
    			'photo1'=>$img1,
    			'photo2'=>$img2,
    			'price'=>$data['price'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId(),
    			'status'=>$data['status'],
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $data['id']);
    	$this->update($arr, $where);
    }
    public function getDriverById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name WHERE id = $id LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
   
    
   function getServiceById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT id,service_title,description,photo,photo1,photo2,price,note,`status`FROM ldc_serviceprice WHERE id=$id";
    	return $db->fetchRow($sql);
    }
    function getAllServicePrice(){
    	$sql="SELECT id,service_title,description,photo,price,`status`FROM ldc_serviceprice WHERE 1";
    	$db=$this->getAdapter();
    	$order=" ORDER BY  id DESC";
    	return $db->fetchAll($sql.$order);
    }
    
}  
	  

