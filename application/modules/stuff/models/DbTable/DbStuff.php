<?php

class Stuff_Model_DbTable_DbStuff extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_stuff';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    function addStuff($_data){
    	
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{

    		$adapter = new Zend_File_Transfer_Adapter_Http();
    		$part= PUBLIC_PATH.'/images/product/';
    		
    		$identity = $_data['identity'];
    		$ids = explode(',', $identity);
    		$image_feature="";
    		$image_list="";
    		$set_image_fea =0;
    		foreach ($ids as $i){
    			$name = $_FILES['photo'.$i]['name'];
    			if (!empty($name)){
    				$ss = 	explode(".", $name);
    				//if(in_array($ext,$valid_formats)) {
    				$image_name = 'car_'.date("Y").date("m").date("d").time().$i.".".end($ss);
    				$tmp = $_FILES['photo'.$i]['tmp_name'];
    				if(move_uploaded_file($tmp, $part.$image_name)){
    					$photo = $image_name;
    				}
    				else
    					$string = "Image Upload failed";
    				if ($set_image_fea==0){
    					$image_feature = $image_name;
    					$set_image_fea=1;
    				}
    				if (empty($image_list )){
    					$image_list=$image_name;
    				}else{$image_list = $image_list.",".$image_name;
    				}
    			}
    		}
	    	$_arr = array(
	    			'equipment_name'=>$_data['Eq_name'],
	    			'reference_no'=>$_data['Referent'],
	    			'url'=>$_data['Url'],
	    			'status'=>$_data['status'],
	    			'images_list'=>$image_list,
	    			'photo_front'=>$image_feature,
// 	    			'photo_front'=>$_data['front_Veiw'],
// 	    			'photo_front_right'=>$_data['Front_Right'],
// 	    			'photo_front_left'=>$_data['front_left'],
// 	    			'photo_rear_left'=>$_data['rear_left'],
	    			'year'=>$_data['year'],
	    			'color'=>$_data['color'],
	    			'model'=>$_data['model'],
	    			'size'=>$_data['size'],
	    			'serial_no'=>$_data['serial_no'],
	    			'other'=>$_data['other'],
	    			'telephone_num'=>$_data['telephone_num'],
	    			'cell_phone'=>$_data['cell_phone'],
	    			'type'=>$_data['type'],
	    			'sim_card'=>$_data['sim_card'],
	    			'user_id'=>$this->getUserId(),
	    			'date'=>date("d-m-Y")
	    			);
	    	 $id = $this->insert($_arr);
	    	 $this->_name='ldc_stuff_details';
	    	 $ids = explode(',', $_data['record_row']);
	    	 $upload=new Zend_File_Transfer();
	    	 foreach ($ids as $i){
	    	 	 $item =array(
	    	 	 	'stuff_id'=>$id,
	    	 		'package_id'=>$_data['package_id'.$i],
		    		'price'=>$_data['price_'.$i],
		    		'extra_price'=>$_data['extracharge_'.$i],
		    		'note'=>$_data['note_'.$i],
		    		'date'=>date("Y-m-d"),
		    		'user_id'=> $this->getUserId()
	    	        );
	    	 	$this->insert($item);
    	       }
    		 $db->commit();
    	 }catch(Exception $e){
    	 	$db->rollBack();
    	 }
      }
      function updateStuff($_data){
      	$db = $this->getAdapter();
      	$db->beginTransaction();
      	try{
      		$adapter = new Zend_File_Transfer_Adapter_Http();
      		$part= PUBLIC_PATH.'/images/product/';
	      	$identity = $_data['identity'];
	    	$ids = explode(',', $identity);
	    	$image_feature="";
	    	$image_list="";
	    	$set_image_fea =0;
	    	foreach ($ids as $i){
	    			$name = $_FILES['photo'.$i]['name'];
	    			if (!empty($name)){
	    				$ss = 	explode(".", $name);
	    				$image_name = 'car_'.date("Y").time().$i.".".end($ss);
	    				$tmp = $_FILES['photo'.$i]['tmp_name'];
	    				if(move_uploaded_file($tmp, $part.$image_name)){
	    					$photo = $image_name;
	    				}
	    				else
	    					$string = "Image Upload failed";
		    				
	    			}else{
	    				$image_name = $_data['old_photo'.$i];
	    			}
	    			
	    			if ($set_image_fea==0){
	    				$image_feature = $image_name;
	    				$set_image_fea=1;
	    			}
	    			if (empty($image_list )){
	    				$image_list=$image_name;
	    			}else{$image_list = $image_list.",".$image_name;
	    			}
    		}
      		
      		$_arr = array(
      				'equipment_name'=>$_data['Eq_name'],
      				'reference_no'=>$_data['Referent'],
      				'url'=>$_data['Url'],
      				'status'=>$_data['status'],
      				'images_list'=>$image_list,
	    			'photo_front'=>$image_feature,
// 	    			'photo_front'=>$_data['front_Veiw'],
// 	    			'photo_front_right'=>$_data['Front_Right'],
// 	    			'photo_front_left'=>$_data['front_left'],
// 	    			'photo_rear_left'=>$_data['rear_left'],
      				'year'=>$_data['year'],
      				'color'=>$_data['color'],
      				'model'=>$_data['model'],
      				'size'=>$_data['size'],
      				'serial_no'=>$_data['serial_no'],
      				'other'=>$_data['other'],
      				'telephone_num'=>$_data['telephone_num'],
      				'cell_phone'=>$_data['cell_phone'],
      				'type'=>$_data['type'],
      				'sim_card'=>$_data['sim_card'],
      				'user_id'=>$this->getUserId()
      		);
      		$where="id=".$_data['id'];
      		$id = $this->update($_arr, $where);
      		$this->_name='ldc_stuff_details';
      		$where = " stuff_id = ".$_data['id'];
      		$this->delete($where);
      		$ids = explode(',', $_data['record_row']);
      		foreach ($ids as $i){
      			$item =array(
      					'stuff_id'=>$_data['id'],
      					'package_id'=>$_data['package_id'.$i],
      					'price'=>$_data['price_'.$i],
      					'extra_price'=>$_data['extracharge_'.$i],
      					'note'=>$_data['note_'.$i],
      					'date'=>date("Y-m-d"),
      					'user_id'=> $this->getUserId()
      			);
      			$this->insert($item);
      		}
      		$db->commit();
      	}catch(Exception $e){
      		$db->rollBack();
      		echo $e->getMessage();
      	}
      }
      function getAllStuff($search){
      	$db = $this->getAdapter();
      	$sql = " SELECT `id`,`equipment_name`,`reference_no`,`year`,model,serial_no,telephone_num,`status` FROM ldc_stuff WHERE 1";
      	$where='';
      	if(!empty($search['title'])){
      		$s_where=array();
      		$s_search=addslashes(trim($search['title']));
      		$s_where[]=" equipment_name LIKE '%{$s_search}%'";
      		$s_where[]=" reference_no LIKE '%{$s_search}%'";
      		$s_where[]=" year LIKE '%{$s_search}%'";
      		$s_where[]=" model LIKE '%{$s_search}%'";
      		$s_where[]=" telephone_num LIKE '%{$s_search}%'";
      		$where.=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['status_search']>-1){
      		$where.= " AND status = ".$search['status_search'];
      	}
      	$order=" ORDER BY id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      function getAllStuffInFrontend(){
      	$db = $this->getAdapter();
      	$sql = " SELECT `id`,`equipment_name`,`reference_no`,`year`,model,serial_no,telephone_num,color,size,type,other,`status`,photo_front,photo_front_right,photo_front_left,photo_rear_left FROM ldc_stuff WHERE status=1 AND equipment_name!='' ";
      	return $db->fetchAll($sql);
      }
      function getPackageById($id){
      	$db = $this->getAdapter();
      	$sql = " SELECT 
				   `ldc_stuff`.`equipment_name`,`ldc_stuff`.`photo_front`,`ldc_stuff`.`photo_front_left`,`ldc_stuff`.`photo_front_right`
				   ,`ldc_stuff`.`photo_rear_left`,`ldc_stuff`.`reference_no`,`ldc_stuff`.`status`,`ldc_stuff`.`url`,`ldc_stuff_details`.`extra_price`
				   ,`ldc_stuff_details`.`package_id`,`ldc_stuff_details`.`price`,`ldc_stuff_details`.`stuff_id`,`ldc_stuff_details`.`note`
				   FROM `ldc_stuff` INNER JOIN `ldc_stuff_details` ON `ldc_stuff`.`id`=`ldc_stuff_details`.`id`  WHERE ldc_stuff.id= ".$id;
      	return $db->fetchRow($sql);
      }
      function  getStuffById($id){
      	$sql="SELECT * FROM ldc_stuff WHERE id=$id";
      	return $this->getAdapter()->fetchRow($sql);
      }
      function  getStuffDetailById($id){
      	$sql="SELECT id,stuff_id,package_id,price,extra_price,note FROM ldc_stuff_details WHERE stuff_id=".$id;
      	return $this->getAdapter()->fetchAll($sql);
      }
   }
   		  
  
