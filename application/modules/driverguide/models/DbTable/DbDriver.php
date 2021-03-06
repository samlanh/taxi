<?php

class Driverguide_Model_DbTable_DbDriver extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_driver';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authcar');
    	return $session_user->user_id;
    }
    
    function getAllDriverGuide($search=null){
    	$db = $this->getAdapter();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$lang= $dbgb->getCurrentLang();
    	$array = array(1=>"province_en_name",2=>"province_kh_name");
    	$arrayview = array(1=>"name_en",2=>"name_kh");
    	$sql = "SELECT id,
    	(SELECT vt.title FROM ldc_vechicletye AS vt  WHERE vt.id=
		(SELECT v.car_type FROM ldc_vehicle AS v WHERE v.id=ldc_driver.vehicle_id LIMIT 1) LIMIT 1)AS car_type,
        (SELECT v.reffer FROM ldc_vehicle AS v WHERE v.id=ldc_driver.vehicle_id LIMIT 1)AS car_number,
    	driver_id,last_name,
    	(SELECT ".$arrayview[$lang]." FROM `ldc_view` WHERE TYPE=1 AND key_code =$this->_name.`sex`) AS sex ,
    	tel,DATE_FORMAT(dob, '%d-%b-%Y') As dob,pob,nationality,
    	group_num,home_num,street,commune,district,
    	(SELECT ".$array[$lang]." FROM `ldc_province` WHERE `ldc_province`.id=province_id LIMIT 1) AS province_name,
    	status
    	FROM $this->_name  ";
    	$where = ' WHERE 1 ';
    	if($search['status_search']>-1){
    		$where.= " AND $this->_name.`status`= ".$search['status_search'];
    	}
    	if(!empty($search['vehicle_type'])){
    		$where.= " AND (SELECT v.car_type FROM ldc_vehicle AS v WHERE v.id=vehicle_id)= ".$search['vehicle_type'];
    	}
    	if($search['province']>-1){
    		$where.= " AND $this->_name.`province_id`= ".$search['province'];
    	}
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['title']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[]="REPLACE(driver_id,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(last_name,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(tel,' ','')  			LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(pob,' ','')  			LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(nationality,' ','')  	LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(group_num,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(home_num,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(street,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(commune,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE(district,' ','')  		LIKE '%{$s_search}%'";
    		$s_where[]="REPLACE((SELECT v.reffer FROM ldc_vehicle AS v WHERE v.id=ldc_driver.vehicle_id LIMIT 1),' ','')  		LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function addDriver($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$adapter = new Zend_File_Transfer_Adapter_Http();
    		$part= PUBLIC_PATH.'/images/driverphoto';
    		$part_p= PUBLIC_PATH.'/images/driverphoto/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$adapter->setDestination($part);
    		$adapter->receive();
    			
    		$photo = $adapter->getFileInfo();
    		$photoname = str_replace(" ", "_", $_data['name_kh'])."_".str_replace(" ", "_", $_data['name_en']);
    		if (!empty($photo['photo']['name'])){
    			$ss = 	explode(".", $photo['photo']['name']);
    			$new_image_name = $photoname.".".end($ss);
    			$tmp = $photo['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part_p.$new_image_name)){
    				$_data['photo'] = $new_image_name;
    			}
    		}else{
    			$_data['photo'] =$_data['old_photo'];
    		}
    		if(!empty($photo['att_file']['name'])){
    			$_data['att_file']=$photo['att_file']['name'];
    		}else{
    			$_data['att_file']="";
    		}
    		
    		if($_data['vehicle_ref_no']!=""){
    			$car=array(
    					'reffer'=>$_data['vehicle_ref_no'],
    					'car_type'=>$_data['vehicle_type'],
    					'status'=>1,
    					'create_date'=>date("Y-m-d H:i:s"),
    					'modify_date'=>date("Y-m-d H:i:s"),
    					);
    			$this->_name="ldc_vehicle";
    			$car_id=$this->insert($car);
    		}else{
    			$car_id=$_data['vehicle'];
    		}
    		
    		$_arr = array(
	    		'driver_id'		=>$_data['client_no'],
	    		//'first_name'	=>$_data['name_kh'],
	    		'last_name'		=>$_data['name_en'],
	    		'sex'			=>$_data['sex'],
	    		'dob'			=>$_data['dob_client'],
	    		'pob'			=>$_data['pob'],
	    		'nationality'	=>$_data['nationality'],
// 	    		'document_type'	=>$_data['client_d_type'],
	    		'doc_number'	=>$_data['national_id'],
	    		'photo'			=>$_data['photo'],
	    		'lang_note'		=>$_data['desc'],
	    		'id_card'		=>$_data['id_card'],
	    		'issue_date'	=>$_data['issued_date'],
	    		'expired_date'	=>$_data['expired_date'],
	    		'register_date'	=>$_data['registered_date'],
	    		'experience_desc'=>$_data['experience'],
	    		'document_file'	=>$_data['att_file'],
	    		'tel'			=>$_data['phone'],
	    		'email'			=>$_data['email'],
	    		'group_num'		=>$_data['group'],
	    		'home_num'		=>$_data['home'],
	    		'street'		=>$_data['street'],
	    		'commune'		=>$_data['commune'],
	    		'district'		=>$_data['district'],
	    		'province_id'	=>$_data['province'],
	    		'date'			=>date("Y-m-d"),
	    		'user_id'		=>$this->getUserId(),
	    		'status'		=>$_data['status'],
    			'create_date'	=>date("Y-m-d H:i:s"),
    			'modify_date'	=>date("Y-m-d H:i:s"),
    			'vehicle_id'	=>$car_id,
    			);
    	$this->_name="ldc_driver";
    	$this->insert($_arr);//insert data
    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    public function updateDriver($_data){
//     	print_r($_data);exit();
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    		$part= PUBLIC_PATH.'/images/driverphoto';
    		$part_p= PUBLIC_PATH.'/images/driverphoto/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$adapter->setDestination($part);
    		$adapter->receive();
    			
    		$photo = $adapter->getFileInfo();
    		$photoname = str_replace(" ", "_", $_data['name_en'])."_".str_replace(" ", "_", $_data['name_en']);
    		if (!empty($photo['photo']['name'])){
    			$ss = 	explode(".", $photo['photo']['name']);
    			$new_image_name = $photoname.".".end($ss);
    			$tmp = $photo['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part_p.$new_image_name)){
    				$_data['photo'] = $new_image_name;
    			}
    		}else{
    			$_data['photo'] =$_data['old_photo'];
    		}
    		if(!empty($photo['att_file']['name'])){
    			$_data['att_file']=$photo['att_file']['name'];
    		}else{
    			$_data['att_file']="";
    		}
	    	
	    	if($_data['vehicle_ref_no']!=""){
	    		$car=array(
	    				'reffer'=>$_data['vehicle_ref_no'],
	    				'car_type'=>$_data['vehicle_type'],
	    				'status'=>1,
	    				'modify_date'=>date("Y-m-d H:i:s"),
	    		);
	    		$this->_name="ldc_vehicle";
	    		$where=" id=".$_data['vehicle_id'];
	    		$car_id=$_data['vehicle_id'];
	    		 if(!empty($car_id)){
	    		 	$this->update($car, $where);
	    		 }else{
	    		 	$car_id=$this->insert($car);
	    		 }
	    		
	    	}else{
	    		$car_id=$_data['vehicle'];
	    	}
	    	
	    	$_arr = array(
	    			'driver_id'		=>$_data['client_no'],
	    			//'first_name'	=>$_data['name_kh'],
	    			'last_name'		=>$_data['name_en'],
	    			'sex'			=>$_data['sex'],
	    			'dob'			=>$_data['dob_client'],
	    			'pob'			=>$_data['pob'],
	    			'nationality'	=>$_data['nationality'],
	    			//'document_type'=>$_data['client_d_type'],
	    			'doc_number'	=>$_data['national_id'],
	    			'photo'			=>$_data['photo'],
	    			'lang_note'		=>$_data['desc'],
	    			'id_card'		=>$_data['id_card'],
	    			'issue_date'	=>$_data['issued_date'],
	    			'expired_date'	=>$_data['expired_date'],
	    			'register_date'	=>$_data['registered_date'],
	    			'experience_desc'=>$_data['experience'],
	    			'document_file'	=>$_data['att_file'],
	    			'tel'			=>$_data['phone'],
	    			'email'			=>$_data['email'],
	    			'group_num'		=>$_data['group'],
	    			'home_num'		=>$_data['home'],
	    			'street'		=>$_data['street'],
	    			'commune'		=>$_data['commune'],
	    			'district'		=>$_data['district'],
	    			'province_id'	=>$_data['province'],
	    			'user_id'		=>$this->getUserId(),
	    			'status'		=>$_data['status'],
	    			'modify_date'	=>date("Y-m-d H:i:s"),
	    			'vehicle_id'	=>$car_id,
	    	);
	    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
	    	$this->_name="ldc_driver";
	    	$this->update($_arr, $where);
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    public function getDriverById($id){
    	$this->_name="ldc_driver";
    	$db = $this->getAdapter();
    	$sql = "SELECT d.*,(SELECT v.reffer FROM ldc_vehicle AS v WHERE v.id=d.vehicle_id LIMIT 1)AS vehicle_ref_no,
    	(SELECT v.car_type FROM ldc_vehicle AS v WHERE v.id=d.vehicle_id LIMIT 1)AS car_type
    	 FROM $this->_name as d WHERE id = $id LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
	 function getBranchById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT br_id,prefix,branch_namekh,branch_nameen,br_address,branch_code,branch_tel,fax,displayby,other,status FROM
    	$this->_name ";
    	$where = " WHERE `br_id`= $id LIMIT 1" ;
  
   		return $db->fetchRow($sql.$where);
    }
    public static function getBranchCode(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT COUNT(br_id) AS amount FROM `ln_branch`";
    	$acc_no= $db->getGlobalDbRow($sql);
    	$acc_no=$acc_no['amount'];
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	$pre = "";
    	for($i = $acc_no;$i<3;$i++){
    		$pre.='0';
    	}
    	return "C-".$pre.$new_acc_no;
    }
    
    function getvehicleinfo($vehilce_id){ //add & edit driver
    	$db = $this->getAdapter();
    	$sql='
    		SELECT v.*,
			m.`title` AS make,
			mo.`title` AS model,
			smo.`title` AS submodel,
			CONCAT(m.`title`," ",mo.`title`," ",smo.`title`," (",v.`reffer`,")") AS `name`,
			(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
			(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
			(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine`
			FROM `ldc_vehicle` AS v,
			`ldc_make` AS m,
			`ldc_model` AS mo,
			`ldc_submodel` AS smo
			WHERE 
			v.`make_id` = m.`id` AND
			v.`model_id` = mo.`id` AND
			v.`sub_model` = smo.`id` AND
			v.is_sale !=1 
			AND v.`status`=1 AND v.`id` ='.$vehilce_id.' LIMIT 1
    	';
    	$row = $db->fetchRow($sql);
    	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
    	$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
    	$string="";
    	if (!empty($row)){
    		if (!empty($row['img_front'])){
    			$images = $baseurl."/images/vehicle/".$row['img_front'];
    		}else{
    			$images = $baseurl."/images/no_car.png";
    		}
    		$string='
    			<div class="col-xs-3">
					<div class=" col-xs-12">
				       <div class="image_car">
                           <img src="'.$images.'" class="preview_carlist" alt="'.$row['reffer'].'">
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class=" col-xs-9  text-left">
				    <h4 class="car_title">
						  '.$row['name'].'			                     
					</h4>
				   <ul class="list-unstyled">
						<li>
					    	<div class="col-md-2 col-sm-2 col-xs-12">
					        	<span class="span_title">'.$tr->translate('Vehicle Ref.No.').'</span>
					       </div> 
					       <div class="col-md-4 col-sm-4 col-xs-12">: 
					       		<span class="span_value">'.$row['reffer'].'</span>
					       </div>
					       <div class="col-md-2 col-sm-2 col-xs-12">
					       		<span class="span_title">'.$tr->translate('YEAR').'</span>
					       </div> 
					       <div class="col-md-4 col-sm-4 col-xs-12">: 
					       		<span class="span_value">'.$row['year'].'</span>
					       </div>
					       <div class="clearfix"></div>
					  </li>
					  <li>
							<div class="col-md-2 col-sm-2 col-xs-12">
								<span class="span_title">'.$tr->translate('Color').'</span>
							</div> 
							<div class="col-md-4 col-sm-4 col-xs-12">: 
								<span class="span_value">'.$row['color'].'</span>
							</div>
							<div class="col-md-2 col-sm-2 col-xs-12">
								<span class="span_title">'.$tr->translate("No. of Seats").'</span>
							</div> 
							<div class="col-md-4 col-sm-4 col-xs-12">: 
								<span class="span_value">'.$row['seat_amount'].'</span>
							</div>
							<div class="clearfix"></div>
						</li>
						<li>
							<div class="col-md-2 col-sm-2 col-xs-12">
								<span class="span_title">'.$tr->translate("Transmission Type").'</span>
							</div> 
							<div class="col-md-4 col-sm-4 col-xs-12">: 
								<span class="span_value">'.$row['transmission'].'</span>
							</div>
							<div class="col-md-2 col-sm-2 col-xs-12"><span class="span_title">'.$tr->translate("Type").'</span></div> 
							<div class="col-md-4 col-sm-4 col-xs-12">: <span class="span_value">'.$row['type'].'</span></div>
							<div class="clearfix"></div>
						</li>
					</ul>
				</div>
    		';
    	}else{
    		$images = $baseurl."/images/no_car.png";
    		$string='
    		<div class="col-xs-3">
	    		<div class=" col-xs-12">
		    		<div class="image_car">
		    			<img src="'.$images.'" class="preview_carlist" alt="">
		    		</div>
	    		</div>
    			<div class="clearfix"></div>
    		</div>
    		<div class=" col-xs-9  text-left">
    			<h4 class="car_title">
	    		</h4>
	    		<ul class="list-unstyled">
		    		<li>
			    		<div class="col-md-2 col-sm-2 col-xs-12">
			    		<span class="span_title">'.$tr->translate('Vehicle Ref.No.').'</span>
			    		</div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">:
			    		<span class="span_value"></span>
			    		</div>
			    		<div class="col-md-2 col-sm-2 col-xs-12">
			    		<span class="span_title">'.$tr->translate('YEAR').'</span>
			    		</div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">:
			    		<span class="span_value"></span>
			    		</div>
			    		<div class="clearfix"></div>
		    		</li>
		    		<li>
			    		<div class="col-md-2 col-sm-2 col-xs-12">
			    		<span class="span_title">'.$tr->translate('Color').'</span>
			    		</div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">:
			    		<span class="span_value"></span>
			    		</div>
			    		<div class="col-md-2 col-sm-2 col-xs-12">
			    		<span class="span_title">'.$tr->translate("No. of Seats").'</span>
			    		</div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">:
			    		<span class="span_value"></span>
			    		</div>
			    		<div class="clearfix"></div>
		    		</li>
		    		<li>
		    		<div class="col-md-2 col-sm-2 col-xs-12">
			    		<span class="span_title">'.$tr->translate("Transmission Type").'</span>
			    		</div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">:
			    		<span class="span_value"></span>
			    		</div>
			    		<div class="col-md-2 col-sm-2 col-xs-12"><span class="span_title">'.$tr->translate("Type").'</span></div>
			    		<div class="col-md-4 col-sm-4 col-xs-12">: <span class="span_value"></span></div>
			    		<div class="clearfix"></div>
		    		</li>
	    		</ul>
    		</div>
    		';
    	}
    	return $string;
    }
    
    function getReffervehicleinfo($vehilce_id){ //add & edit driver
    	$db = $this->getAdapter();
    	$sql='
    	SELECT v.*,
    	(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`
    	FROM `ldc_vehicle` AS v
    	WHERE  v.`id` ='.$vehilce_id.' LIMIT 1 ';
    	$row = $db->fetchRow($sql);
    	return $row;
    }
    
}  
	  
