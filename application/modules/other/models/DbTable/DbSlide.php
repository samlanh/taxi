<?php

class Other_Model_DbTable_DbSlide extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_website_setting';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    function getWebsiteSetting($label){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM `vd_website_setting` AS ws WHERE ws.`label`='$label' AND ws.`status`=1";
    	return $db->fetchRow($sql);
    }
// 	function updateSlide($_data,$label_name){
// 		$db = $this->getAdapter();
//     	$db->beginTransaction();
//     	try{
//     		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
//     		$part= PUBLIC_PATH.'/images/slide/';
//     		if ($label_name=='slide_partner'){
//     			$part= PUBLIC_PATH.'/images/slide/partner/';
//     		}
    		
//     		$identity = $_data['identity'];
//     		$image_list="";
//     		$ids = explode(',', $identity);
//     		foreach ($ids as $i){
//     			$name = $_FILES['photo'.$i]['name'];
//     			if (!empty($name)){
//     				$ss = 	explode(".", $name);
//     				$image_name = date("Y").time().$i.".".end($ss);
//     				$tmp = $_FILES['photo'.$i]['tmp_name'];
//     				if(move_uploaded_file($tmp, $part.$image_name)){
//     					$photo = $image_name;
//     				}
//     				else
//     					$string = "Image Upload failed";
    				 
//     			}else{
//     				$image_name = $_data['old_photo'.$i];
//     			}
//     			if (empty($image_list )){
//     				$image_list=$image_name;
//     			}else{
//     				$image_list = $image_list.",".$image_name;
//     			}
//     		}
//     		$_arr=array(
//     				'value'      => $image_list,
//     				'date_modify'  =>date("Y-m-d"),
//     				'status'=>1,
//     				'user_id'      => $this->getUserId(),
//     		);
//     		$this->_name="vd_website_setting";
//     		$where=" label= '".$label_name."'";
//     		$this->update($_arr, $where);
//     		$db->commit();
//     	}catch(exception $e){
//     		Application_Form_FrmMessage::message("Application Error");
//     		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
//     		$db->rollBack();
//     	}
// 	}
    function getSlideShow(){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM `vd_slideshow` AS ws";
    	return $db->fetchAll($sql);
    }
    function updateslideshow($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbg = new Application_Model_DbTable_DbGlobal();
    		$part= PUBLIC_PATH.'/images/slide/';
    		$identity = $_data['identity'];
    		$ids = explode(',', $identity);
    		$image_name='';
    			
    		$detail_list="";
    		foreach ($ids as $i){
    			if (!empty($_data['detail_id'.$i])){
    				if (empty($detail_list)){
    					$detail_list=$_data['detail_id'.$i];
    				}else{$detail_list=$detail_list.",".$_data['detail_id'.$i];
    				}
    			}
    		}
    		if (!empty($detail_list)){
    			$this->_name="vd_slideshow";
    			$where = "id NOT IN (".$detail_list.")";
    			$this->delete($where);
    		}
    		foreach ($ids as $i){
    			if (!empty($_data['detail_id'.$i])){
    				if (!empty($_FILES['photo'.$i]['name'])){
    					$ss = 	explode(".", $_FILES['photo'.$i]['name']);
    					$new_image_name = date("Y").date("m").date("d").time().$i.".".end($ss);
    					$image_name = $dbg->resizeImase($_FILES['photo'.$i], $part,$new_image_name);
    				}else{
    					$image_name = $_data['old_photo'.$i];
    				}
    				$_arr=array(
    						'title'      => $_data['title'.$i],
    						'images'      => $image_name,
    						'link'  =>"",
    						'create_date'=>date("Y-m-d H:i:s"),
    						'modify_date'=>date("Y-m-d H:i:s"),
    						'user_id'      => $this->getUserId(),
    				);
    				$this->_name="vd_slideshow";
    				$where = ' id = '.$_data['detail_id'.$i];
    				$this->update($_arr, $where);
    			}else{
    				if (!empty($_FILES['photo'.$i]['name'])){
    					$ss = 	explode(".", $_FILES['photo'.$i]['name']);
    					$new_image_name = date("Y").date("m").date("d").time().$i.".".end($ss);
    					$image_name = $dbg->resizeImase($_FILES['photo'.$i], $part,$new_image_name);
    				}else{
    					$image_name = "";
    				}
    				$_arr=array(
    						'title'      => $_data['title'.$i],
    						'images'      => $image_name,
    						'link'  =>"",
    						'create_date'=>date("Y-m-d H:i:s"),
    						'modify_date'=>date("Y-m-d H:i:s"),
    						'user_id'      => $this->getUserId(),
    				);
    				$this->_name="vd_slideshow";
    				$this->insert($_arr);
    			}
    		}
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
	function updateAnnouncement($_data,$label_name){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$identity = $_data['identity'];
    		$ids = explode(',', $identity);
			$value='';
    		foreach ($ids as $i){
				if(empty($value)){$value= $_data['article'.$i];}elseif(!empty($value)){ $value= $value.",".$_data['article'.$i];}
			}
				$_arr=array(
    				'value'      => $value,
    				'date_modify'  =>date("Y-m-d"),
    				'status'=>1,
    				'user_id'      => $this->getUserId(),
    		);
			$this->_name="vd_website_setting";
    		$where=" label= '".$label_name."'";
			$this->update($_arr, $where);
			$db->commit();
		}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function homecontent($_data,$label_name){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$dbglobal = new Application_Model_DbTable_DbVdGlobal();
			$lang = $dbglobal->getLaguage();
			$value='';
			if(!empty($lang)) foreach($lang as $row){
				$title = str_replace(' ','',$row['title']);
				$description = preg_replace("/(\\/[^>]*>)([^<]*)(<)/","\\1\\3",str_replace('"',"'",trim($_data['description'.$title])));
				if(empty($value)){
					$value= '{"'.$title.'": "'.$_data['title'.$title].'","description'.$title.'": "'.$description.'"}';
				}else{ $value= $value.',{"'.$title.'":"'.$_data['title'.$title].'","description'.$title.'": "'.$description.'"}';
				}
			}
			$_arr=array(
					'value'      => '{"values":['.$value.']}',
					'date_modify'  =>date("Y-m-d"),
					'status'=>1,
					'user_id'      => $this->getUserId(),
			);
			$this->_name="vd_website_setting";
			$where=" label= '".$label_name."'";
			$this->update($_arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

