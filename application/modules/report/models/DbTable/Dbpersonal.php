<?php
class Report_Model_DbTable_Dbpersonal extends Zend_Db_Table_Abstract
{
      
      
    public function getPaymentSchedule($id){
    	$db=$this->getAdapter();
    	$sql = "SELECT * FROM `ln_loanmember_funddetail` WHERE member_id= $id";
    	return $db->fetchAll($sql);
    }
	
}

