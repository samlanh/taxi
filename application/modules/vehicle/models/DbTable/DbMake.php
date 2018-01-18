<?php

class Vehicle_Model_DbTable_DbMake extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_make';
    function addMake($data){
    	$title = str_replace(" ", "_",$data['make']);
   		$part= PUBLIC_PATH.'/images/vehicle/make/';
    	$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = 	explode(".", $name);
    			$image_name = $title.".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}
    	$_arr = array(
    			'title'=>$data['make'],
    			'status'=>1,
    			'images'=>$photo
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateMake($data){
    	$title = str_replace(" ", "_",$data['make']);
    	$part= PUBLIC_PATH.'/images/vehicle/make/';
    	$photo="";
    	$name = $_FILES['photo']['name'];
    	if (!empty($name)){
    		$ss = 	explode(".", $name);
    		$image_name = $title.".".end($ss);
    		$tmp = $_FILES['photo']['tmp_name'];
    		if(move_uploaded_file($tmp, $part.$image_name)){
    			$photo = $image_name;
    		}
    		else
    			$string = "Image Upload failed";
    	}else{
    		$photo = $data['old_photo'];
    	}
    	$_arr = array(
    			'title'=>$data['make'],
    			'status'=>$data['status'],
    			'images'=>$photo
    	);
    	$where='id='.$data['id'];
    	$this->update($_arr, $where);
    }
    	
    function getAllMake($search=null){
    	$db = $this->getAdapter();
    	$sql='SELECT id,title,status
    	FROM ldc_make WHERE 1 ';
    	$order=' ORDER BY id DESC';
        return $db->fetchAll($sql.$order);
    }
    
 function getMakeById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM  $this->_name WHERE id=".$id;
   		return $db->fetchRow($sql);
    }
//     public static function getBranchCode(){
//     	$db = new Application_Model_DbTable_DbGlobal();
//     	$sql = "SELECT COUNT(br_id) AS amount FROM `ln_branch`";
//     	$acc_no= $db->getGlobalDbRow($sql);
//     	$acc_no=$acc_no['amount'];
//     	$new_acc_no= (int)$acc_no+1;
//     	$acc_no= strlen((int)$acc_no+1);
//     	$pre = "";
//     	for($i = $acc_no;$i<3;$i++){
//     		$pre.='0';
//     	}
//     	return "C-".$pre.$new_acc_no;
//     }
  
}  
	  

