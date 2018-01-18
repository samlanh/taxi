<?php

class agreement_Model_DbTable_DbAgreement extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_vehicleagreement';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    public function getSystemSetting($keycode){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM `ln_system_setting` WHERE keycode ='".$keycode."' LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function getLastAgreementCode(){
    	$db = $this->getAdapter();
    	$row = $this->getSystemSetting('agreement_code');
    	$sql=" SELECT COUNT(id)​ FROM `ldc_vehicleagreement` LIMIT 1 ";
    	$number = $db->fetchOne($sql);
    	$new_no= (int)$number+101;
    	$number= strlen((int)$number+1);
    	$sub='';
    	for($i = $number;$i<6;$i++){
    		$sub.='0';
    	}
    	return $sub.$new_no;
    }
    public function getNewAgreementCode($date=null){
    	$db = $this->getAdapter();
    	$row = $this->getSystemSetting('agreement_code');
    	$sql=" SELECT COUNT(id)​ FROM `ldc_vehicleagreement` LIMIT 1 ";
    	$number = $db->fetchOne($sql);
    	$new_no= (int)$number+101;
    	$number= strlen((int)$number+1);
    	$sub='';
    	for($i = $number;$i<6;$i++){
    		$sub.='0';
    	}
    	if($date==null){
    		$sub=date("y")."-".date("m")."-".date("d")."-".$sub.$new_no;
    	}else{
    		$sub=date("y",strtotime($date))."-".date("m",strtotime($date))."-".date("d",strtotime($date))."-".$sub.$new_no;
    	    
    	}
    	
        $pre = ($row['value']);
    	return $pre."-".$sub;
    	
    }
    
    function getAllAgreement($data){
    	$db = $this->getAdapter();
    	$sql ="SELECT id,agreement_code,(SELECT owner_name FROM `ldc_owner` WHERE id=ownder_id LIMIT 1) AS owner_name,
		    	(SELECT CONCAT(first_name,' ',last_name) FROM `ldc_customer` WHERE id=$this->_name.customer_id LIMIT 1) AS customer_name,
		    	booking_id,
		    	(SELECT reffer FROM `ldc_vehicle` WHERE id=$this->_name.vehicle_id LIMIT 1) AS reffer,
		    	inception_date,return_date,return_time,period ,price_perday
    			FROM $this->_name ORDER BY id DESC";
    	return $db->fetchAll($sql);
    }
    function getAgreementById($id){
    	$db = $this->getAdapter();
    	$sql ="SELECT * FROM $this->_name WHERE id= $id ";
    	return $db->fetchRow($sql);
    }
    function addAgreement($data){
    	$db = $this->getAdapter();
    	$agreement_code = $this->getNewAgreementCode($data['agreement_date']);
    	$arr = array(
                'vat_owner'=>$data['vat_owner'],
    			'vat_customer'=>$data['vat_customer'],
    			'agreement_code'=>$agreement_code,
    			'agreement_date'=>$data['agreement_date'],
    			'booking_id'=>$data['booking_id'],
    			'ownder_id'=>$data['owner_name'],
    			'customer_id'=>$data['customer_id'],
    			'vehicle_id'=>$data['vehicle_ref_no'],
    			'inception_date'=>$data['incep_date'],
    			'return_date'=>$data['return_date'],
    			'return_time'=>$data['return_time'],
    			'period'=>$data['period'],
    			'price_perday'=>$data['price_day'],
    			'vat_amount'=>$data['total_vat'],
    			
    			'amount_price'=>$data['amount_price'],
    			'discount_value'=>$data['discount_value'],
    			'discount_percent'=>$data['discount_percent'],
    			'refundable'=>$data['amount_f_d'],
    			'longdist_acc'=>$data['longdast'],
    			'grand_total'=>$data['grand_total'],
    			'paid_amount'=>$data['paid_amount'],
    			'due_amount'=>$data['due_amount'],
    			
    			'date_create'=>date("d-m-Y"),
    			'user_id'=>$this->getUserId(),
    			'regular_id'=>$data['regular_maintanance'],
    			'unlimited'=>$data['unlimited_mileage'],
    			'repare'=>$data['repair_spare_part'],
    			'insurance'=>$data['insurance_coverage'],
    			'fule'=>$data['fuel'],
    			'fuel_full'=>$data['fuel_full_tank'],
    			'art1_id'=>$data['article'],
    			'toart1_id'=>$data['toart1_id'],
    			'art2_id'=>$data['art2_id'],
    			'toart2_id'=>$data['toart2_id'],
    			'art3_id'=>$data['art3_id'],
    			'toart3_id'=>$data['toart3_id'],
                        
                        'sunday_price'=>$data['sunday_price'],
    			'airport_price'=>$data['airport_price'],
    			'dropairport_price'=>$data['dropairport_price'],
    			'item_1'=>$data['item_1'],
    			'item_2'=>$data['item_2'],
    			'item_3'=>$data['item_3'],
    			
    			'sunday_remark'=>$data['sunday_remark'],
    			'airport_remark'=>$data['airport_remark'],
    			'dropairport_remark'=>$data['dropairport_remark'],
    			'item_1remark'=>$data['item_1remark'],
    			'item_2remark'=>$data['item_2remark'],
    			'item_3remark'=>$data['item_3remark'],
                        'is_passport'=>empty($data['passport'])?0:1,
    			'is_idcard'=>empty($data['idcard'])?0:1,
    			'is_familybook'=>empty($data['familybook'])?0:1,
'witness'=>$data['witness']
    			);
    	if(!empty($data['id'])){
    		$where=" id= ".$data['id'];
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
	function getBookingInfoById($data){
		$db = $this->getAdapter();
		$sql="SELECT b.*,c.* FROM ldc_booking AS b,ldc_customer AS c WHERE 
				c.id=b.customer_id AND b.id = ".$data['booking_id']." LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getVehiclByBookingId($data){
		$db = $this->getAdapter();
		$sql="SELECT item_id ,price,VAT ,discount,deposite,refund_deposit,total,total_paymented,item_type
		 		FROM `ldc_booking_detail` WHERE book_id=$data AND item_type=1 LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getOwnerById($id){
		$db = $this->getAdapter();
		$sql = "SELECT id,owner_name,`position`,id_card,hand_phone,email,hotline,`status` FROM ldc_owner where id= ".$id;
		return $db->fetchRow($sql);
	}
}

