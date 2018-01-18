<?php
class Report_Model_DbTable_DbloanCollect extends Zend_Db_Table_Abstract
{
      
       protected  $db_name='ln_loanmember_funddetail';
    public function getAllLnClient($search=null){
    	$db=$this->getAdapter();
    	$start_date = $search['start_date'];
   		$end_date = $search['end_date'];
    	$sql = "SELECT * FROM v_newloancolect WHERE 1";
    	$where ='';
    	if(!empty($search['start_date']) or !empty($search['end_date'])){
    		$where.=" AND date_payment BETWEEN '$start_date' AND '$end_date'";
    	}
    	if($search['branch_id']>0){
    		$where.=" AND branch_id = ".$search['branch_id'];
    	}
    	if($search['client_name']>0){
    		$where.=" AND client_id = ".$search['client_name'];
    	}
    	if($search['co_id']>0){
    		$where.=" AND co_id = ".$search['co_id'];
    	}
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = $search['adv_search'];
    		$s_where[] = "branch_kh LIKE '%{$s_search}%'";
    		$s_where[] = " co_name LIKE '%{$s_search}%'";
    		$s_where[] = " client_name LIKE '%{$s_search}%'";
    		$s_where[] = " total_principal LIKE '%{$s_search}%'";
    		$s_where[] = " principal_permonth LIKE '%{$s_search}%'";
    		$s_where[] = " total_interest LIKE '%{$s_search}%'";
    		$s_where[] = " amount_day LIKE '%{$s_search}%'";
    		$where .=' AND '.implode(' OR ',$s_where).'';
    	}
    	return $db->fetchAll($sql.$where);
    	
    }
	public function latepayment($search=null){
		$db=$this->getAdapter();
		$pay_date = $search['payment_date'];
		$late_date = $search['late_date'];
		$sql="SELECT * FROM v_getloancollects WHERE 1";
		$late_pay_date=$late_date-$pay_date;
	}
	
	public function getLoanCollectionByCo($search=null){
		$db = $this->getAdapter();
		$start_date = $search['start_date'];
		$end_date = $search['end_date'];
		try{
			$sql="SELECT 
					  b.`branch_namekh`,
					  CONCAT(co.`co_code`, '-',co.`co_khname`,',',co.`co_firstname`,' ',co.`co_lastname`) AS co_name,
					  c.`receipt_no`,
					  c.`date_input`,
					  c.`principal_amount`,
					  c.`recieve_amount`,
					  c.`return_amount`,
					  c.`penalize_amount`,
					  c.`service_charge`,
					  c.`total_interest`,
					  c.`total_payment`,
					  c.`amount_payment`,
					  c.`total_principal_permonth`,
					  cm.`date_payment`,
					  cm.`principal_permonth`,
					  cm.`remain_capital`,
					  cm.`capital`,
					  cm.`penelize_amount`,
					  cm.`service_charge`,
					  cm.`total_interest`,
					  cm.`total_payment`,
					  cm.`loan_number`,
					  cm.`total_recieve`,
					  cm.`pay_after`,
					  lc.`name_kh`,
					  lc.`phone`,
					  lc.`client_number`,
					  lm.`total_capital`,
					  lm.`interest_rate`,
					  lg.`total_duration`,
					  lg.`date_release`,
					  (SELECT
					     `ln_view`.`name_en`
					   FROM `ln_view`
					   WHERE ((`ln_view`.`type` = 14)
					          AND (`ln_view`.`key_code` = `lg`.`pay_term`))) AS `Term Borrow`
					FROM
					  `ln_client_receipt_money` AS c,
					  `ln_co` AS co,
					  `ln_branch` AS b ,
					  `ln_client_receipt_money_detail` AS cm,
					  `ln_client` AS lc,
					  `ln_loan_member` AS lm,
					  `ln_loan_group` AS lg
					WHERE c.`co_id` = co.`co_id` 
					AND c.id=cm.`crm_id`
					  AND c.`branch_id`=b.`br_id`
					  ";
			
			$where ='';
	      	if(!empty($search['advance_search'])){
	      		//print_r($search);
	      		$s_where = array();
	      		$s_search = $search['advance_search'];
	      		$s_where[] = "lcrm.`loan_number` LIKE '%{$s_search}%'";
	      		$s_where[] = " lcrm.`receipt_no` LIKE '%{$s_search}%'";
	      		$s_where[] = " lcrm.`total_payment` LIKE '%{$s_search}%'";
	      		$s_where[] = " lcrm.`total_interest` LIKE '%{$s_search}%'";
	      		$s_where[] = " lcrm.`penalize_amount` LIKE '%{$s_search}%'";
	      		$s_where[] = " lcrm.`service_charge` LIKE '%{$s_search}%'";
	      		$where .=' AND ('.implode(' OR ',$s_where).')';
	      	}
	      	if($search['status']!=""){
	      		$where.= " AND status = ".$search['status'];
	      	}
	      	 
	      	if(!empty($search['start_date']) or !empty($search['end_date'])){
	      		$where.=" c.`date_input` BETWEEN '$start_date' AND '$end_date'";
	      	}
	      	if($search['branch_id']>0){
	      		$where.=" AND lcrm.`branch_id`= ".$search['branch_id'];
	      	}
	      	if($search['co_id']>0){
	      		$where.=" AND co.`co_id`= ".$search['co_id'];
	      	}
	      	if($search['paymnet_type']>0){
	      		$where.=" AND lcrm.`payment_option`= ".$search['paymnet_type'];
	      	}
	      	 
	      	//$where='';
	      	$order = " ORDER BY lcrm.currency_type";
	      	//echo $sql.$where.$order;
	      	return $db->fetchAll($sql.$where.$order);
			
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
}

