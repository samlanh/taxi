<?php

class Other_Model_DbTable_DbVideo extends Zend_Db_Table_Abstract
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
	
	function updateVideo($_data,$label_name){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$identity = $_data['identity'];
    		$ids = explode(',', $identity);
			$value='';
    		foreach ($ids as $i){
				if(empty($value)){$value= $_data['frame'.$i];}else{ $value= $value.",".$_data['frame'.$i];}
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
	function updateAnnouncement($_data,$label_name){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$identity = $_data['identity'];
    		$ids = explode(',', $identity);
			$value='';
    		foreach ($ids as $i){
				if(empty($value)){$value= $_data['article'.$i];}else{ $value= $value.",".$_data['article'.$i];}
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
}

