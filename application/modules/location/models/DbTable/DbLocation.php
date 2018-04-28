<?php

class Location_Model_DbTable_DbLocation extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_package_location';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    public function addPackage($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$part= PUBLIC_PATH.'/images/location/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		
	    	$_arr=array(
	    		    'location_name' => $_data['location_name'],
	    			'province_id' => $_data['province_name'],
	    			'status'  => $_data['status'],
	    			'note'    => $_data['note'],
	    			'date'    => date("Y-m-d"),
	    			'create_date'    => date("Y-m-d H:i:s"),
	    			'modify_date'    => date("Y-m-d H:i:s"),
	    			'user_id' => $this->getUserId(),
// 	    			'service_type'=>$_data['service_type'],
// 	    			'locationtype_id'=>$_data['location_type'],
	    			'create_date'=> date("Y-m-d H:i"),
	    	);
	    	$id =  $this->insert($_arr);
	    	
	    	$this->_name='ldc_package_detail';
	    	$arr = array(
	    			'package_id'=>$id,
	    			'location_id'=>$id,
	    			'description'=>$_data['note'],
	    			'status'=>$_data['status'],
	    			'date'=>date("Y-m-d"),
	    			'user_id'=> $this->getUserId()
	    			);
	    	 $this->insert($arr);
	    	
	    	$this->_name='ldc_picture_location';
	    	$photoname = str_replace(" ", "_", $_data['location_name'].'-location');
	    	$ids = explode(',', $_data['record_row']);
	    	$image_name="";
		    	foreach ($ids as $i){
		    		if (!empty($_FILES['photo'.$i]['name'])){
		    			$ss = 	explode(".", $_FILES['photo'.$i]['name']);
		    			$new_image_name = $photoname.$i.".".end($ss);
		    				$tmp = $_FILES['photo'.$i]['tmp_name'];
    						if(move_uploaded_file($tmp, $part.$new_image_name)){
    							$image_name = $new_image_name;
    						}
		    		}else{
		    			$image_name ="";
		    		}
		    		
		    		$arr = array(
		    				'location_id'=>$id,
		    				'pic_title'=>$image_name,
		    				'status'=>$_data['status'],
		    				'date'=>date("Y-m-d")
		    		);
		    		$this->insert($arr);
		    	}
	    	$db->commit();
    	}catch(Exception $e){
    		$db->rollBack();
    		echo $e->getMessage();
    	}
    	
    }
    
    public function updatePackage($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$part= PUBLIC_PATH.'/images/location/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		
    		$_arr=array(
    				'location_name' => $_data['location_name'],
    				'province_id' => $_data['province_name'],
    				'status'  => $_data['status'],
    				'note'    => $_data['note'],
    				'date'    => date("Y-m-d"),
    				'modify_date'    => date("Y-m-d H:i:s"),
    				'user_id' => $this->getUserId(),
//     				'service_type'=>$_data['service_type'],
//     				'locationtype_id'=>$_data['location_type'],
    		);
    		$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    		$this->update($_arr, $where);
    		
    
    		$this->_name='ldc_package_detail';
    		$arr = array(
//     				'package_id'=>$id,
//     				'location_id'=>$id,
    				'description'=>$_data['note'],
    				'status'=>$_data['status'],
    				'date'=>date("Y-m-d"),
    				'user_id'=> $this->getUserId()
    		);
    
    		$where=$this->getAdapter()->quoteInto("package_id=?", $_data['id']);
    		$this->update($arr, $where);
    		
//     		$ids = explode(',', $_data['record_row']);
//     		$detail_list="";
//     		foreach ($ids as $i){
//     			if (!empty($_data['old_photoid'.$i])){
//     				if (empty($detail_list)){
//     					$detail_list=$_data['old_photoid'.$i];
//     				}else{$detail_list=$detail_list.",".$_data['old_photoid'.$i];
//     				}
//     			}
//     		}
//     		if (!empty($detail_list)){
//     			$this->_name="ldc_picture_location";
//     			$where = "id NOT IN (".$detail_list.")";
//     			$this->delete($where);
//     		}
    		
//     		$photoname = str_replace(" ", "_", $_data['location_name'].'-location');
//     		$image_name='';
//     			foreach ($ids as $i){
//     				if (!empty($_data['old_photoid'.$i])){
//     					if (!empty($_FILES['photo'.$i]['name'])){
//     						$ss = 	explode(".", $_FILES['photo'.$i]['name']);
//     						$new_image_name = $photoname.$i.".".end($ss);
//     						$tmp = $_FILES['photo'.$i]['tmp_name'];
//     						if(move_uploaded_file($tmp, $part.$new_image_name)){
//     							$image_name = $new_image_name;
//     						}
//     					}else{
//     						$image_name = $_data['old_photo'.$i];
//     					}
//     					$arr = array(
//     							'location_id'=> $_data['id'],
//     							'pic_title'=>$image_name,
//     							'status'=>$_data['status'],
//     							'date'=>date("Y-m-d")
//     					);
//     					$where = ' id = '.$_data['old_photoid'.$i];
// 						$this->update($arr, $where);
//     				}else{
//     					if (!empty($_FILES['photo'.$i]['name'])){
//     						$ss = 	explode(".", $_FILES['photo'.$i]['name']);
//     						$new_image_name = $photoname.$i.".".end($ss);
//     						$tmp = $_FILES['photo'.$i]['tmp_name'];
//     						if(move_uploaded_file($tmp, $part.$new_image_name)){
//     							$image_name = $new_image_name;
//     						}
    					
//     					}else{
//     						$image_name = "";
//     					}
//     					$arr = array(
//     							'location_id'=> $_data['id'],
//     							'pic_title'=>$image_name,
//     							'status'=>$_data['status'],
//     							'date'=>date("Y-m-d")
//     					);
//     					$this->insert($arr);
//     				}
    				
//     			}

    		$db->commit();
    	}catch(Exception $e){
    		$err=$e->getMessage();
    		Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		$db->rollBack();
    		
    	}
    	 
    }
	public function getLocationById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM ldc_package_location WHERE id = ".$id;
		$sql.=" LIMIT 1";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getPhotoDetailById($id){
		$db = $this->getAdapter();
		$sql = "SELECT id,pic_title FROM `ldc_picture_location` WHERE location_id=".$id;
		$rows=$db->fetchAll($sql);
		return $rows;
	}
    public function updateProvince($_data,$id){
    	$_arr=array(
    			'province_name' => $_data['kh_province'],
    			'date'      => date("Y-d-m"),
    			'status'           => $_data['status'],
    			'user_id'	       => $this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $id);
    	$this->update($_arr, $where);
    }
    function getAllLocations($search=null){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$array_ser = array(1=>"title_en",2=>"title_kh");
    	$sql = " SELECT  id,location_name, (SELECT ".$array[$lang]." FROM `ldc_province` WHERE id=province_id) AS province_name,
                 modify_date,$this->_name.`status`
                 FROM $this->_name WHERE is_package !=1 AND location_name!='' ";
    	$where = '';
    	if(!empty($search['title'])){
    		$s_search=addslashes(trim($search['title']));
			$s_search = str_replace(' ', '', $s_search);
			$s_where[]="REPLACE(location_name,' ','')   LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if($search['status_search']>-1){
    		$where.= " AND status = ".$db->quote($search['status_search']);
    	}
    	if($search['province']>-1){
    		$where.= " AND province_id = ".$db->quote($search['province']);
    	}
    	$order=" order by id DESC";
    	//     	$arrayview = array(1=>"name_en",2=>"name_kh");
    	//     	(SELECT ".$array_ser[$lang]." FROM ldc_service_type as st WHERE st.id = service_type limit 1) as service_type,
    	//     	(SELECT lt.title FROM `ldc_locationtype` AS lt WHERE lt.id = ldc_package_location.`locationtype_id` LIMIT 1) AS location_type,
    	//     	if(!empty($search['service_type'])){
    	//     		$where.= " AND service_type = ".$db->quote($search['service_type']);
    	//     	}
    	//     	if($search['location_type']>-1){
    	//     		$where.= " AND locationtype_id = ".$db->quote($search['location_type']);
    	//     	}
    	//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$order);
    }
    /*----------------------------package----------------------------*/
    function getAllPackages($search=null){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$sql = " SELECT  id,location_name, (SELECT ".$array[$lang]." FROM `ldc_province` WHERE id=province_id) AS province_name,
    	date,$this->_name.`status`
    	FROM $this->_name WHERE location_name!='' AND is_package=1 ";
    	$order=" order by id DESC";
    	$where = '';
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['title']);
    		$s_where[]=" location_name LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if($search['status']>-1){
    		$where.= " AND status = ".$db->quote($search['status']);
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
    function getAllLocationByPackage($package_id){//using in index
    	$db = $this->getAdapter();
    	$sql = " SELECT location_id,province_id,
					(SELECT location_name FROM `ldc_package_location` WHERE STATUS=1 AND is_package!=1 AND id =location_id) AS location_name
 				FROM `ldc_package_detail` WHERE package_id=$package_id AND status=1";
    	return $db->fetchAll($sql);
    }
	public function addNewPackageLocation($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->setDestination(PUBLIC_PATH."/images/location");
			$fileinfo=$adapter->getFileInfo();
			$adapter->receive();
	
			$_arr=array(
					'province_id' => $_data['province_name'],
					'location_name' => $_data['location_name'],
					'is_package' => 1,
					'service_type'=>1,
					'status'  => $_data['status'],
					'note'    => $_data['note'],
					'date'    => date("Y-m-d"),
					'user_id' => $this->getUserId()
			);
			$id =  $this->insert($_arr);
			
			$this->_name='ldc_package_detail';
			$ids = explode(',', $_data['record_row']);
			foreach ($ids as $i){
				$arr = array(
						'package_id'=>$id,
						'location_id'=>empty($_data['location_id'.$i])?0:$_data['location_id'.$i],
						'description'=>$_data['note'],
						'status'=>$_data['status'],
						'date'=>date("Y-m-d"),
						'user_id'=> $this->getUserId()
				);
				$this->insert($arr);
			}
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			echo $e->getMessage();
		}
	}
	public function updateNewPackageLocation($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->setDestination(PUBLIC_PATH."/images/location");
			$fileinfo=$adapter->getFileInfo();
			$adapter->receive();
	
			$_arr=array(
					'province_id' => $_data['province_name'],
					'location_name' => $_data['location_name'],
					'service_type'=>1,
					'is_package' => 1,
					'status'  => $_data['status'],
					'note'    => $_data['note'],
					'date'    => date("Y-m-d"),
					'user_id' => $this->getUserId()
			);
			$where=$this->getAdapter()->quoteInto("id=?",  $_data['id']);
			$this->update($_arr, $where);
			
			$this->_name='ldc_package_detail';
			$where=$this->getAdapter()->quoteInto("package_id=?",  $_data['id']);
			$this->delete($where);
				
			$this->_name='ldc_package_detail';
			$ids = explode(',', $_data['record_row']);
			foreach ($ids as $i){
				$arr = array(
						'package_id'=>$_data['id'],
						'location_id'=>empty($_data['location_id'.$i])?0:$_data['location_id'.$i],
						'description'=>$_data['note'],
						'status'=>$_data['status'],
						'date'=>date("Y-m-d"),
						'user_id'=> $this->getUserId()
				);
				$this->insert($arr);
			}
			$db->commit();
		}catch(Exception $e){
			$err=$e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($err);
			echo $e->getMessage();
			$db->rollBack();
		}
			
	}
   
}

