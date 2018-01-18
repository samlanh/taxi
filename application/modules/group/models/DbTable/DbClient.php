<?php

class Group_Model_DbTable_DbClient extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_customer';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    	 
    }
	public function addClient($_data){
		$photoname = str_replace(" ", "_", $_data['name_en'].'-AGN') . '.jpg';
		$upload = new Zend_File_Transfer();
		$upload->addFilter('Rename',
				array('target' => PUBLIC_PATH . '/images/profile/'. $photoname, 'overwrite' => true) ,'photo');
		$receive = $upload->receive();
		if($receive)
		{
			$_data['photo'] = $photoname;
		}
		else{
			$_data['photo']=!empty($_data['id'])?$_data['id']:"";
		}
		
		try{
			$db = new Application_Model_DbTable_DbGlobal();
			$client_code = $db->getNewClientId();
			
		$_arr=array(
				'title'	  => $_data['title'],
				'customer_code'	  => $client_code,//$_data['client_no'],
				'first_name'	  =>$_data['name_kh'],
				'last_name' => $_data['name_en'],
				'sex'=> $_data['sex'],
				'dob'	  => $_data['dob_client'],
				'pob'	  => $_data['country'],
				'occupation'	  => $_data['occupation'],
				'nationality'	  => $_data['nationality'],
				'company_name'=> $_data['company_name'],
				'customer_type'	  => $_data['customer_type'],
				'photo'	  => $_data['photo'],
				'note'	      => $_data['desc'],
				'passport_name'			=>$_data['passport'],
				'pass_issuedate'  => $_data['pissue_date'],
				'pass_expireddate'      => $_data['pexpired_date'],
				'card_name'      => $_data['card_code'],
				'card_issuedate'      => $_data['cissue_date'],
				'card_expireddate'  => $_data['cexpired_date'],
				'ftb'	  => $_data['ftb'],
				'ftb_issuedate'	      => $_data['fissue_date'],
				'ftb_expireddate'  =>$_data['fexpired_date'],
				'phone'        =>$_data['phone'],
				'email'=>$_data['email'],
				'fax'	      => $_data['fax'],
				'group_num' => $_data['group_num'],
				'house_num'=>$_data['address'],
				'street'=>$_data['street'],
				'commune'=>$_data['commune'],
				'district' => $_data["district"], 
				'province_id'	  => $_data['province'],
				'balance'  => $_data['balance'],
				//'i_housenum'      => $_data['i_house'],
				'i_city'      => $_data['i_city'],
				//'i_province'      => $_data['i_province'],
				'i_zipcode'      => $_data['i_zipcode'],
				//'i_phone'      => $_data['i_phone'],
				//'i_note'	  =>$_data['i_note'],
				'status'  => $_data['status'],
				'date'  => date("Y-m-d"),
		);
		if (!empty($_data['create_acc'])){
			$_arr['password']=md5($_data['password']);
			$_arr['email_login']=$_data['email_login'];
		}
		if(!empty($_data['id'])){
			if (!empty($_data['changepass'])){
				$_arr['password']=md5($_data['password']);
			}
			$where = 'id = '.$_data['id'];
			$this->update($_arr, $where);
			return $_data['id'];
			 
		}else{
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getClientById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientDetailInfo($id){
		$db = $this->getAdapter();
		$sql = "SELECT c.client_id ,c.is_group,group_code, c.client_number ,c.name_kh ,c.name_en,c.join_with ,c.join_nation_id,c.relate_with, 
		c.join_tel, c.guarantor_with ,c.guarantor_tel ,nation_id,
		c.position_id ,(SELECT commune_name FROM `ln_commune` WHERE com_id = c.com_id   LIMIT 1) AS commune_name
		,(SELECT district_name FROM `ln_district` AS ds WHERE dis_id = c.dis_id  LIMIT 1) AS district_name
		,(SELECT province_en_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name
		,(SELECT village_name FROM `ln_village` WHERE vill_id = c.village_id  LIMIT 1) AS village_name ,c.street,c.house ,
		c.id_type ,c.id_number, c.phone,c.job , c.spouse_name , c.spouse_nationid ,c.remark ,c.status , c.user_id ,
		(SELECT name_en FROM `ln_view` WHERE TYPE = 5 AND key_code = c.sit_status) AS sit_status , 
		(SELECT branch_nameen FROM `ln_branch` WHERE br_id =c.branch_id LIMIT 1) AS branch_name ,
		(SELECT name_en FROM `ln_client` WHERE client_id =c.parent_id ) AS parent , 
		(SELECT name_en FROM `ln_view` WHERE TYPE = 11 AND key_code =c.sex) AS sex ,
		(SELECT name_en FROM `ln_view` WHERE TYPE = 23 AND key_code =c.`client_d_type`) AS client_d_type ,
		(SELECT name_en FROM `ln_view` WHERE TYPE = 23 AND key_code =c.`join_d_type`) AS join_d_type ,  
		(SELECT name_en FROM `ln_view` WHERE TYPE = 23 AND key_code =c.`guarantor_d_type`) AS guarantor_d_type ,`guarantor_address`,      
		 photo_name FROM `ln_client` AS c WHERE client_id =  ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientCallateralBYId($client_id){
		$db = $this->getAdapter();
		$sql = " SELECT cc.id AS client_coll ,cd.* FROM `ln_client_callecteral` AS cc , `ln_client_callecteral_detail` AS cd WHERE  
		         cd.is_return=0 AND cd.client_coll_id = cc.id AND cc.client_id = ".$client_id;
		return $db->fetchAll($sql);
	}
    function getViewClientByGroupId($group_id){
    	$db = $this->getAdapter();
    	$sql=" SELECT * FROM $this->_name WHERE client_id=
    	(SELECT client_id FROM `ln_loan_member` WHERE group_id=".$db->quote($group_id)." LIMIT 1)";
    	$row=$db->fetchRow($sql);
    	return $row;
    }
	function getAllClients($search = null){		
		$db = $this->getAdapter();
		$from_date =(empty($search['start_date']))? '1': "date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "date <= '".$search['end_date']." 23:59:59'";
// 		$where = " WHERE (first_name!='' OR  last_name!='') AND ".$from_date." AND ".$to_date;		
		$where = " WHERE (first_name!='' OR  last_name!='') ";
		$sql = " SELECT id,customer_code,first_name,last_name,
		(SELECT name_en FROM `ldc_view` WHERE TYPE=1 AND key_code =$this->_name.`sex` LIMIT 1) AS sex,
		(SELECT name_en FROM `ldc_view` WHERE TYPE=9 AND key_code =ldc_customer.`customer_type` LIMIT 1) AS custype
		,dob,phone,pob,nationality,company_name,
			group_num,house_num,commune,district,
		(SELECT province_en_name FROM `ldc_province` WHERE `ldc_province`.id=province_id LIMIT 1) AS province_name,
		status
	    FROM $this->_name ";
		if(!empty($search['title'])){
			$s_where = array();
			$s_search = addslashes(trim($search['title']));
			$s_where[] = "customer_code LIKE '%{$s_search}%'";
			$s_where[] = " first_name LIKE '%{$s_search}%'";
			$s_where[] = " last_name LIKE '%{$s_search}%'";
			$s_where[] = "phone LIKE '%{$s_search}%'";
			$s_where[] = "pob LIKE '%{$s_search}%'";
			$s_where[] = " nationality LIKE '%{$s_search}%'";
			$s_where[] = " company_name LIKE '%{$s_search}%'";
			$s_where[] = " group_num LIKE '%{$s_search}%'";
			$s_where[] = " house_num LIKE '%{$s_search}%'";
			$s_where[] = " commune LIKE '%{$s_search}%'";
			$s_where[] = " district LIKE '%{$s_search}%'";
// 			$s_where[] = " province_name LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status_search']>-1){
			$where.= " AND status = ".$search['status_search'];
		}
		if($search['customer_type']>-1){
			$where.= " AND customer_type = ".$search['customer_type'];
		}

		$order=" ORDER BY id DESC";
		return $db->fetchAll($sql.$where.$order);	
	}
	public function getGroupCodeBYId($data){
		$db = $this->getAdapter();
		if($data['is_group']==1){
			$sql = "SELECT COUNT(client_id) AS number FROM `ln_client`
			      WHERE is_group =1 AND branch_id = ".$data['branch_id'] ;
			    $acc_no = $db->fetchOne($sql);
				$new_acc_no= (int)$acc_no+1;
				$acc_no= strlen((int)$acc_no+1);
				$pre ="G".$this->getPrefixCode($data['branch_id']);
				for($i = $acc_no;$i<3;$i++){
					$pre.='0';
				}
				return $pre.$new_acc_no;
		}else{
			$sql = " SELECT group_code FROM `ln_client`
			WHERE client_id = ".$data['group_id'] ;
			 $rs = $db->fetchOne($sql);
			if(empty($rs)){return ''; }else{
				return $rs;
			}
		}
		
	}
	function getPrefixCode($branch_id){
		$db  = $this->getAdapter();
		$sql = " SELECT prefix FROM `ln_branch` WHERE br_id = $branch_id  LIMIT 1";
		return $db->fetchOne($sql);
	}	
	public function getClientCode($data){//for get client by branch
		$db = $this->getAdapter();
			$sql = "SELECT COUNT(client_id) AS number FROM `ln_client`
			WHERE branch_id = ".$data['branch_id'] ;
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre =$this->getPrefixCode($data['branch_id']);
		for($i = $acc_no;$i<6;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
// 	public function adddoocumenttype($data){
		
// 		$db = $this->getAdapter();
// 		$document_type=array(
// 				'name_en'=>$data['clienttype_nameen'],
// 				'name_kh'=>$data['clienttype_namekh'],
// 				'displayby'=>1,
// 				'type'=>23,
// 				'status'=>1
				
// 		);
		
// 		$row= $this->insert($document_type);
// 		return $row;
// 	}
	public function addIndividaulClient($_data){
		
		$client_code = $this->getClientCode($_data['branch_id']);
			$_arr=array(
					'is_group'=>0,
					'group_code'=>'',
					'parent_id'=>0,
					'client_number'=>$client_code,
					'name_kh'	  => $_data['name_kh'],
					'name_en'	  => $_data['name_en'],
					'sex'	      => $_data['sex'],
					'sit_status'  => $_data['situ_status'],
					'dis_id'      => $_data['district'],
					'village_id'  => $_data['village'],
					'street'	  => $_data['street'],
					'house'	      => $_data['house'],
					'branch_id'  => $_data['branch_id'],
					'job'        =>$_data['job'],
					'phone'	      => $_data['phone'],
					'create_date' => date("Y-m-d"),
					'client_d_type'      => $_data['client_d_type'],
					'user_id'	  => $this->getUserId(),
					'dob'			=>$_data['dob_client'],	
					'pro_id'      => $_data['province'],
					'com_id'      => $_data['commune'],
					
			
			);
			
				//echo $this->insert($_arr);exit();
				$this->_name = "ln_client";
				$id =$this->insert($_arr);
				return array('id'=>$id,'client_code'=>$client_code);
	}
}

