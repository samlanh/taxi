<?php

class Group_Model_DbTable_DbCustomer extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_carrental_customer';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    	 
    }
    
    function getAllCustomer($search = null){
        $db = $this->getAdapter();
        $sql = "  SELECT c.id,c.cus_no,c.customer,c.nationality,c.phone,c.passport,c.address,
       (SELECT first_name FROM rms_users WHERE rms_users.id=c.user_id LIMIT 1) AS user_name,c.status
                   FROM `ldc_carrental_customer`  AS c where 1 ";
        
//         $from_date =(empty($data['start_date']))? '1': " c.`date_in` >= '".$data['start_date']."'";
//         $to_date = (empty($data['end_date']))? '1': "   c.`date_in` <= '".$data['end_date']."'";
//         $where = " AND ".$from_date." AND ".$to_date
        $where='';
        if(!empty($search['adv_search'])){
            $s_where = array();
            $s_search = addslashes(trim($search['adv_search']));
            $s_search = str_replace(' ', '', $s_search);
            $s_where[] = "REPLACE(cus_no,' ','')   LIKE '%{$s_search}%'";
            $s_where[] = "REPLACE(customer,' ','') LIKE '%{$s_search}%'";
            $s_where[] = "REPLACE(nationality,' ','') LIKE '%{$s_search}%'";
            $s_where[] = "REPLACE(phone,' ','') LIKE '%{$s_search}%'";
            $s_where[] = "REPLACE(passport,' ','') LIKE '%{$s_search}%'";
            $s_where[] = "REPLACE(address,' ','') LIKE '%{$s_search}%'";
            $where .=' AND ('.implode(' OR ',$s_where).')';
        }
        if($search['status']>-1){
            $where.= " AND c.status = ".$search['status'];
        }
        $order=" ORDER BY c.id DESC";
        return $db->fetchAll($sql.$where.$order);
    }
    
	public function addCustomer($_data){
// 		$photoname = str_replace(" ", "_", $_data['name_en'].'-AGN') . '.jpg';
// 		$upload = new Zend_File_Transfer();
// 		$upload->addFilter('Rename',
// 				array('target' => PUBLIC_PATH . '/images/profile/'. $photoname, 'overwrite' => true) ,'photo');
// 		$receive = $upload->receive();
// 		if($receive)
// 		{
// 			$_data['photo'] = $photoname;
// 		}
// 		else{
// 			$_data['photo'] = $_data['old_photo'];
// 		}
		try{
			$db = new Application_Model_DbTable_DbGlobal();
			$client_code = $db->getcustomerno();
		$_arr=array(
		        'cus_no'      => $client_code,
				'customer'    => $_data['lessee_name'],
				'sex'         => 1,
				'nationality' => $_data['nationality'],
				'phone'	      => $_data['phone'],
		        'passport'	  => $_data['passport'],
				'address'	  => $_data['address'],
		        'create_date' => date("Y-m-d H:i:s"),
		        'modify_date' => date("Y-m-d H:i:s"),
		        'status'      => $_data['status'],
				'user_id'	  => $this->getUserId(),
		);
	    $this->insert($_arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function editCustomer($_data){
	    // 		$photoname = str_replace(" ", "_", $_data['name_en'].'-AGN') . '.jpg';
	    // 		$upload = new Zend_File_Transfer();
	    // 		$upload->addFilter('Rename',
	    // 				array('target' => PUBLIC_PATH . '/images/profile/'. $photoname, 'overwrite' => true) ,'photo');
	    // 		$receive = $upload->receive();
	    // 		if($receive)
	        // 		{
	        // 			$_data['photo'] = $photoname;
	        // 		}
	    // 		else{
	    // 			$_data['photo'] = $_data['old_photo'];
	    // 		}
	    try{
	        $db = new Application_Model_DbTable_DbGlobal();
	        $client_code = $db->getcustomerno();
	        $_arr=array(
	            'cus_no'      => $_data['cus_no'],
	            'customer'    => $_data['lessee_name'],
	            'sex'         => 1,
	            'nationality' => $_data['nationality'],
	            'phone'	      => $_data['phone'],
	            'passport'	  => $_data['passport'],
	            'address'	  => $_data['address'],
	            'create_date' => date("Y-m-d H:i:s"),
	            'modify_date' => date("Y-m-d H:i:s"),
	            'status'      => $_data['status'],
	            'user_id'	  => $this->getUserId(),
	        );
	        $where=" id=".$_data['id'];
	        $this->update($_arr,$where);
	    }catch(Exception $e){
	        Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	    }
	}
	
	function addCustomerAjax($data){
		$db = new Application_Model_DbTable_DbGlobal();
		$client_code = $db->getNewClientId();
		$_arr=array(
				'title'	  	=> $data['title'],
				'customer_code'=> $client_code,//$_data['client_no'],
				//'first_name'=>$data['name_kh'],
				'last_name' =>$data['name_en'],
				'sex'		=>$data['sex'],
				'phone'     =>$data['phone'],
				'email'		=>$data['email'],
				'status'	=>1,
				'user_id'	=>$this->getUserId(),
				);
		$id =$this->insert($_arr);
		if ($id){
			$arr=array('id'=>$id,'cus_code'=>$client_code);
		}
		return $arr;
	}
	
	
	function addLocationAjax($data){
		$this->_name='ldc_package_location';
		$string=$data['str_value'];
		$_arr=array(
				'location_name'	=>$data['location_name'],
				'province_id' 	=>$data['province_name'],
				'note'			=>$data['descript'],
				'status'		=>1,
				'user_id'		=>$this->getUserId(),
		);
		$id=$this->insert($_arr);
		if($id){
			$arr=array(
					'id'=>$id,
					'str_value'=>$string
					);
		}
		return $arr;
	}
 
 
    function getViewClientByGroupId($group_id){
    	$db = $this->getAdapter();
    	$sql=" SELECT * FROM $this->_name WHERE client_id=
    	(SELECT client_id FROM `ln_loan_member` WHERE group_id=".$db->quote($group_id)." LIMIT 1)";
    	$row=$db->fetchRow($sql);
    	return $row;
    }
    
    function getCustomerById($cus_id){
        $db = $this->getAdapter();
        $sql="SELECT * FROM `ldc_carrental_customer` WHERE id=$cus_id";
        $row=$db->fetchRow($sql);
        return $row;
    }
    
	
}
