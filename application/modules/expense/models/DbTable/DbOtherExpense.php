<?php

class Expense_Model_DbTable_DbOtherExpense extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_expense';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
    }
    
    function getAllOtherExpense($search = null){
    	$db = $this->getAdapter();
    	$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	 
    	$sql = " SELECT  id,invoice,cheque_no,
			         (SELECT name_en FROM tb_view WHERE `key_code`=ln_expense.payment_type AND `type`=15 LIMIT 1) AS payment_type,title,
			         CONCAT('$ ',total_amount),create_date,
			        (SELECT first_name FROM rms_users WHERE rms_users.id=ln_expense.user_id LIMIT 1) AS user_name,`status`
			        FROM ln_expense ";
    	$from_date =(empty($search['start_date']))? '1': " create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " WHERE ".$from_date." AND ".$to_date;
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(invoice,' ','') LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(title,' ','') LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(cheque_no,' ','')  	LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status_search']>-1){
    		$where.= " AND status = ".$search['status_search'];
    	}
    	if($search['payment_method']>0){
    		$where.= " AND payment_type = ".$search['payment_method'];
    	}
    	$order=" ORDER BY id DESC";
    	//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$order);
    }
    
	public function addExpense($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr_data=array(
					"invoice"    	=>$data['invoice'],
					"cheque_no" 	=>$data['cheque_num'],
					"title"  		=>$data['title'],
					"payment_type"  =>$data['payment_method'],
					"total_amount"  =>$data['total_amount'],
					"description"   =>$data['note_de'],
					"date"  	 	=>date("Y-m-d"),
					'create_date' 	=>date("Y-m-d"),
					'user_id'       =>$this->getUserId(),
					"status"		=>$data['Stutas'],
			);
			$this->_name="ln_expense";
			$expense_id = $this->insert($arr_data);
				
			$ids=explode(',',$data['record_row']);
			foreach ($ids as $key => $i)
			{
				$data_item= array(
						'expense_id' 		=> 	$expense_id,
						'expense_type_id' 	=> 	$data['expense_id_'.$i],
						'total_amount'     	=>		$data['price_'.$i],
						'description'      	=>		$data['note_'.$i],
						'status'      		=> 1,
				);
				$this->_name='ln_expense_detail';
				$this->insert($data_item);
			}
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			Application_Form_FrmMessage::message('INSERT_FAIL');
			$err =$e->getMessage();
			echo $err;exit();
			Application_Model_DbTable_DbUserLog::writeMessageError($err);
		}
	}
	
	public function updateExpense($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr_data=array(
					"invoice"    	=>$data['invoice'],
					"cheque_no" 	=>$data['cheque_num'],
					"title"  		=>$data['title'],
					"payment_type"  =>$data['payment_method'],
					"total_amount"  =>$data['total_amount'],
					"description"   =>$data['note_de'],
					"date"  	 	=>date("Y-m-d"),
					'create_date' 	=>date("Y-m-d"),
					'user_id'       =>$this->getUserId(),
					"status"		=>$data['Stutas'],
			);
			$this->_name="ln_expense";
			$where=" id=".$data['id']; 
			$this->update($arr_data, $where);

			$sql =" DELETE FROM ln_expense_detail WHERE expense_id=".$data["id"];
			$db->query($sql);
			$ids=explode(',',$data['record_row']);
			foreach ($ids as $key => $i)
			{
				$data_item= array(
						'expense_id' 		=> 	$data['id'],
						'expense_type_id' 	=> 	$data['expense_id_'.$i],
						'total_amount'     	=>	$data['price_'.$i],
						'description'      	=>	$data['note_'.$i],
						'status'      		=>  1,
				);
				$this->_name='ln_expense_detail';
				$this->insert($data_item);
			}
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			Application_Form_FrmMessage::message('INSERT_FAIL');
			$err =$e->getMessage();
			echo $err;exit();
			Application_Model_DbTable_DbUserLog::writeMessageError($err);
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
	
	function getViewsAsName($type=2){
		$db=$this->getAdapter();
		$lang= $this->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT key_code as id ,".$array[$lang]." as name FROM tb_view WHERE `type`=$type";
		return $db->fetchAll($sql);
	}
	
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		if(!empty($session_lang->lang_id)){
			return $session_lang->lang_id;
		}else{
			return 2;
		}
	}
	
	function getVehicleRefNo(){
		$db=$this->getAdapter();
		$sql="	SELECT id,reffer AS `name`  FROM ldc_vehicle WHERE  `status`=1 ORDER BY id DESC";
		return $db->fetchAll($sql);
	}
	
	function getVehiclAndDriver($id){
		$db=$this->getAdapter();
		$sql="SELECT 
		      d.driver_id,d.first_name,d.last_name,d.sex,d.photo,d.dob,d.nationality,d.tel,d.id_card,
		      v.reffer,v.year,
		      
		      (SELECT vt.title FROM ldc_vechicletye AS vt WHERE vt.id=v.car_type LIMIT 1) AS cat_type       
		      
		     FROM ldc_driver AS d,ldc_vehicle AS v
		     WHERE v.id=d.vehicle_id AND d.id=11 ";
		return $db->fetchRow($sql);
	}
	
	function getExpendVehicleType($type){
		$db=$this->getAdapter();
		$sql=" SELECT id,account_name AS name FROM ln_expense_type WHERE option_type=$type AND `status`=1 order by id desc";
		return $db->fetchAll($sql);
	}
	
	public function getAllExpenseByType($type){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$rows = $this->getExpendVehicleType($type);
		$options = '';
		$options .= '<option value="0">'.$tr->translate("SELECT_CATEGORY").'</option>';
		$options .= '<option value="-1">'.$tr->translate("ADD_NEW").'</option>';
		if(!empty($rows))foreach($rows as $value){
			$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['name']).'</option>';
		}
		return $options;
	}
	
	function getExpenseById($id){
		$db=$this->getAdapter();
		$sql=" SELECT * FROM ln_expense WHERE id=$id";
		return $db->fetchRow($sql);
	}
	
	function getExpenseDetail($id){
		$db=$this->getAdapter();
		$sql=" SELECT id,expense_type_id,description,total_amount FROM ln_expense_detail WHERE expense_id=$id";
		return $db->fetchAll($sql);
	}
		
}

