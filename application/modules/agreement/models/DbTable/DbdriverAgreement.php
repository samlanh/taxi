<?php

class agreement_Model_DbTable_DbdriverAgreement extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_driveragreement';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    function getAllAgreement($data){
    	$db = $this->getAdapter();
    	$sql ="SELECT id,agreement_code,(SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) AS owner_name,
				(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=ldc_driveragreement.customer_id LIMIT 1) 
				AS customer_name,
				booking_id,start_date,finish_date,
				period,rental_fee,refundable,grand_total,paid_amount,due_amount  FROM $this->_name  ORDER BY id DESC ";
    	return $db->fetchAll($sql);
    }
    function getAgreementById($id){
    	$db = $this->getAdapter();
    	$sql ="SELECT * FROM $this->_name WHERE id= $id ";
    	return $db->fetchRow($sql);
    }
    function addDriverAgreement($data){
        $dbagree = new agreement_Model_DbTable_DbAgreement();
    	$agreement_code = $dbagree->getNewAgreementCode($data['agreement_date']);
    	$arr = array(
    			'vat_owner'=>$data['vat_owner'],
    			'vat_customer'=>$data['vat_customer'],
    			'agreement_code'=>$agreement_code,
    			'agreement_date'=>$data['agreement_date'],
    			'booking_id'=>$data['booking_id'],
    			'ownder_id'=>$data['owner_name'],
    			'customer_id'=>$data['customer_id'],
    			'start_date'=>$data['incep_date'],
    			'finish_date'=>$data['return_date'],
    			'period'=>$data['period'],
    			'rental_fee'=>$data['rental_fee'],
    			'refundable'=>$data['refundable'],
    			'grand_total'=>$data['grand_total'],
    			'paid_amount'=>$data['paid_total'],
    			'due_amount'=>$data['balance'],
    			'date_create'=>date("d-m-Y"),
    			'user_id'=>$this->getUserId(),
    			'art1_id'=>$data['article'],
    			'toart1_id'=>$data['toart1_id'],
                       'is_passport'=>empty($data['passport'])?0:1,
    			'is_idcard'=>empty($data['idcard'])?0:1,
    			'is_familybook'=>empty($data['familybook'])?0:1,
    			'witness'=>$data['witness']
    			);
    	if(!empty($data['id'])){
    		$where =" id =".$data['id'];
    		$this->update($arr, $where);
    	}else{
    		$this->insert($arr);
    	}

    }
	public function getAllBookingNumber(){
		$db = $this->getAdapter();
		$sql=" SELECT id,booking_no FROM `ldc_booking` WHERE booking_no!=''";
		return $db->fetchAll($sql);
	}
	function getDriverbookingInfoById($data){
		$db = $this->getAdapter();
		$sql="SELECT b.*,c.* FROM ldc_booking AS b,ldc_customer AS c WHERE 
				c.id=b.customer_id AND b.id = ".$data['booking_id']." LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getDriverByBooking($booking_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `ldc_booking_detail` WHERE book_id=$booking_id AND item_type=2 ";
		return $db->fetchAll($sql);
	}
	
}
