<?php

class Bookings_Model_DbTable_DbBooking extends Zend_Db_Table_Abstract
{
	protected $_name ="ldc_stuff";
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
    	$cud = $db->getUserId();
    	return $cud;
	
	}
	function getCustomerInfor($client_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT c.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =c.`sex` LIMIT 1) AS sexs
		FROM ldc_customer AS c WHERE id=".$client_id;
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
		
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$string="";
		if (!empty($row)){
			if (!empty($row['photo'])){
				$images = $baseurl."/images/profile/".$row['photo'];
			}else{
				$images = $baseurl."/images/profile.jpg";
			}
			$string='
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="image-box infor">
						<img id="profile_wiew" src="'.$images.'" alt=""  />
					</div>
				</div>
				<div class="col-md-8 col-sm-8 col-xs-12">
					<ul class="list-unstyled">
                   		<li>
                   			<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value">'.$row['first_name'].' '.$row['last_name'].'</span>
                   		</li>
                    	<li>
                        	<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value">'.$row['sexs'].'</span>
                   		</li>
                   		<li>
                   			<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value">'.$row['nationality'].'</span>
                        </li>
                        <li>
                        	<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['phone'].'</span>
                        </li>
                   	</ul>
				</div>
			';
		}else{
			$images = $baseurl."/images/profile.jpg";
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<ul class="list-unstyled">
               		<li>
                    	<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value"></span>
                    </li>
                    <li>
                    	<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value"></span>
                    </li>
                    <li>
                        <span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value"></span>
                    </li>
                    <li>
                       	<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value"></span>
                    </li>
                 </ul>
			</div>
			';
		}
		 return $string;
	}
	function getAgencyInfor($client_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT c.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =c.`sex` LIMIT 1) AS sexs
		FROM ldc_agency AS c WHERE id=".$client_id;
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
	
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$images = $baseurl."/images/profile.jpg";
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<ul class="list-unstyled">
					<li>
						<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value"></span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value"></span>
					</li>
				</ul>
			</div>
			';
		if (!empty($row)){
			if (!empty($row['photo'])){
				$images = $baseurl."/images/agent/".$row['photo'];
			}
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<ul class="list-unstyled">
					<li>
						<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value">'.$row['first_name'].' '.$row['last_name'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value">'.$row['sexs'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value">'.$row['nationality'].'</span>
					</li>
					<li>
						<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['phone'].'</span>
					</li>
				</ul>
			</div>
			';
		}
		return $string;
	}
	
	function getDriverAndCarInfor($data){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT d.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =d.`sex` LIMIT 1) AS sexs
		 FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!=''
		";
		if ($data['type']==1){
			$sql.=" AND d.id=".$data['id'];
		}else if ($data['type']==2){
			$sql.=" AND d.vehicle_id=".$data['id'];
		}
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
	
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$vehicleid = 0;
		$driver_id = 0;
		
		$images_car = $baseurl."/images/no_car.png";
		$images = $baseurl."/images/profile.jpg";
		
		$vehicle_string='
		<h4 class="car_title"></h4>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
				<img id="profile_wiew" src="'.$images_car.'" alt=""  />
			</div>
		</div>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
				<li>
					<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value"></span>
				</li>
			</ul>
		</div>
		';
		$string='
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
				<img id="profile_wiew" src="'.$images.'" alt=""  />
			</div>
		</div>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
				<li>
					<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value"></span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value"></span>
				</li>
			</ul>
		</div>
		';
		if (!empty($row)){
			$vehicleid = $row['vehicle_id'];
			$driver_id = $row['id'];
			if (!empty($row['photo'])){
				$images = $baseurl."/images/driverphoto/".$row['photo'];
			}
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="image-box infor">
					<img id="profile_wiew" src="'.$images.'" alt=""  />
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
				<li>
					<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value">'.$row['first_name'].' '.$row['last_name'].'</span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value">'.$row['sexs'].'</span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value">'.$row['nationality'].'</span>
				</li>
				<li>
					<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['tel'].'</span>
				</li>
			</ul>
			</div>
			';
			$vehicle =	$this->getvehicleinfo($row['vehicle_id']);
			if (!empty($vehicle)){
				if (!empty($vehicle['img_front'])){
					$images_car = $baseurl."/images/vehicle/".$vehicle['img_front'];
				}
				$vehicle_string='
					<h4 class="car_title">'.$vehicle['make'].' '.$vehicle['model'].' '.$vehicle['submodel'].'</h4>
		       				<div class="col-md-4 col-sm-4 col-xs-12">
				               	<div class="image-box infor">
									<img id="profile_wiew" src="'.$images_car.'" alt=""  />
								</div>
							</div>
							<div class="col-md-8 col-sm-8 col-xs-12">
								<ul class="list-unstyled">
                              		<li>
                              			<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value">'.$vehicle['reffer'].'</span>
                              		</li>
                              		<li>
                              			<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value">'.$vehicle['year'].'</span>
                              		</li>
                              		<li>
                              			<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value">'.$vehicle['color'].'</span>
                              		</li>
                              	</ul>
							</div>
					';
			}
		}
		$array = array(
				'driver'=>$string,
				'vehicle'=>$vehicle_string,
				'driver_id'=>$driver_id,
				'vehicle_id'=>$vehicleid,
				);
		return $array;
	}
	
	function getCarInfor($data){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
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
		AND v.`status`=1 AND v.`id` ='.$data['id'].' LIMIT 1
		';
		$row = $db->fetchRow($sql);
	     
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$vehicleid = 0;
		$images_car = $baseurl."/images/no_car.png";
			$vehicle_string='
			<h4 class="car_title"></h4>
			<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
			<img id="profile_wiew" src="'.$images_car.'" alt=""  />
			</div>
			</div>
			<div id="vehiclesss" class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
			<li>
			<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value"></span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value"></span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value"></span>
			</li>
			</ul>
			</div>
			';
		 
		if (!empty($row)){
			$vehicleid = $row['id'];
			if (!empty($row['img_front'])){
				$images_car = $baseurl."/images/vehicle/".$row['img_front'];
			}
			$vehicle_string='
			<h4 class="car_title">'.$row['make'].' '.$row['model'].' '.$row['submodel'].'</h4>
			<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
			<img id="profile_wiew" src="'.$images_car.'" alt=""  />
			</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
			<li>
			<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value">'.$row['reffer'].'</span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value">'.$row['year'].'</span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value">'.$row['color'].'</span>
			</li>
			</ul>
			</div>
			';
		}else{
			$images_car = $baseurl."/images/no_car.png";
			$vehicle_string='
			<h4 class="car_title"></h4>
			<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
			<img id="profile_wiew" src="'.$images_car.'" alt=""  />
			</div>
			</div>
			<div id="vehiclesss" class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
			<li>
			<span class="span_title">'.$tr->translate("Vehicle Ref.No.").'</span> : <span class="span_value"></span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('YEAR').'</span> : <span class="span_value"></span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('Color').'</span> : <span class="span_value"></span>
			</li>
			</ul>
			</div>
			';
		}
		return $vehicle_string;
	}
	
	function getDriverInfor($data){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql="
		SELECT d.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =d.`sex` LIMIT 1) AS sexs
		FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!='' AND d.id=".$data['id'];
		$order=' LIMIT 1';
		$row = $db->fetchRow($sql.$order);
		
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$baseurl= Zend_Controller_Front::getInstance()->getBaseUrl();
		$vehicleid = 0;
		$driver_id = 0;
		$images_car = $baseurl."/images/no_car.png";
		$images = $baseurl."/images/profile.jpg";
		
		$string='
		<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="image-box infor">
		<img id="profile_wiew" src="'.$images.'" alt=""  />
		</div>
		</div>
		<div class="col-md-8 col-sm-8 col-xs-12">
		<ul class="list-unstyled">
		<li>
		<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value"></span>
		</li>
		<li>
		<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value"></span>
		</li>
		<li>
		<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value"></span>
		</li>
		<li>
		<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value"></span>
		</li>
		</ul>
		</div>
		';
		
		if (!empty($row)){
			$driver_id = $row['id'];
			if (!empty($row['photo'])){
				$images = $baseurl."/images/driverphoto/".$row['photo'];
			}
			$string='
			<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="image-box infor">
			<img id="profile_wiew" src="'.$images.'" alt=""  />
			</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
			<ul class="list-unstyled">
			<li>
			<span class="span_title">'.$tr->translate('NAME').'</span> : <span class="span_value">'.$row['first_name'].' '.$row['last_name'].'</span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('Gender').'</span> : <span class="span_value">'.$row['sexs'].'</span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('Nationality').'</span> : <span class="span_value">'.$row['nationality'].'</span>
			</li>
			<li>
			<span class="span_title">'.$tr->translate('PHONE').'</span> : <span class="span_value">'.$row['tel'].'</span>
			</li>
			</ul>
			</div>
			';
		}
		
		$array = array(
				'driver'=>$string,
				'driver_id'=>$driver_id,
		);
		return $array;
	}
	
	function getAllCarBooking($search){
		$db = $this->getAdapter();
		$glob=new Application_Model_DbTable_DbGlobal();
		$lang= $glob->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$from_date=$search["from_book_date"];
		$to_date=$search["to_book_date"];
		$sql=" SELECT cb.id,cb.*,cb.`booking_no`,c.last_name,c.phone,c.email,
			(SELECT g.last_name FROM ldc_agency AS g WHERE g.id=cb.agency_id LIMIT 1) AS agency_name,
			(SELECT v.title FROM ldc_vechicletye AS v WHERE v.id=cb.vehicletype_id LIMIT 1) AS vehicle_type,
			l.`location_name` AS from_location,
			tl.`location_name` AS to_location,
			cb.`booking_date`,
			cb.`delivey_date`,
			cb.`price`,cb.`commision_fee`,cb.`other_fee`,cb.`total`,
			(SELECT CONCAT(d.`first_name`,' ',d.`last_name`) FROM `ldc_driver` AS d WHERE d.`id` = cb.`driver_id` LIMIT 1) AS driver,cb.driver_fee,
			(SELECT $array[$lang] FROM tb_view AS v WHERE v.key_code=cb.status_working AND v.type=17 LIMIT 1) book_status,
			cb.`status`
			FROM `ldc_carbooking` AS cb,
			`ldc_package_location` AS l,
			`ldc_package_location` AS tl,
			ldc_customer AS c
			WHERE 
			c.id=cb.customer_id   
			AND l.`id` = cb.`from_location`
			AND tl.`id` = cb.`to_location`
			AND cb.`status` >-1 ";
		$where = '';
		
		if($search['date_type']==2){
			$from_date =(empty($search['from_book_date']))? '1': "cb.`delivey_date` >= '".$search['from_book_date']." 00:00:00'";
			$to_date = (empty($search['to_book_date']))? '1': "cb.`delivey_date` <= '".$search['to_book_date']." 23:59:59'";
		}
		if($search['date_type']==1){
			$from_date =(empty($search['from_book_date']))? '1': "cb.`booking_date` >= '".$search['from_book_date']." 00:00:00'";
			$to_date = (empty($search['to_book_date']))? '1': "cb.`booking_date` <= '".$search['to_book_date']." 23:59:59'";
		}
		$where = "  AND ".$from_date." AND ".$to_date;
		if($search["search_text"] !=""){
			$s_where=array();
			$s_search=addslashes(trim($search['search_text']));
			$s_search = str_replace(' ', '', $s_search);
			$s_where[]="REPLACE(c.last_name,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(c.phone,' ','')  LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(c.email,' ','')  LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(cb.`booking_no`,' ','')     LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(tl.`location_name`,' ','')  LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(l.`location_name`,' ','')   LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(cb.`price`,' ','')          LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(cb.`commision_fee`,' ','')  LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(cb.`other_fee`,' ','')      LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(cb.`total`,' ','')          LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		 
		 
		if ($search['agency_search']>0){
			$where.=" AND cb.`agency_id`=".$search['agency_search'];
		}
		if ($search['vehicle_type']>0){
			$where.=" AND cb.`vehicletype_id`=".$search['vehicle_type'];
		}
		if ($search['working_status']>-1){
			$where.=" AND cb.`status_working`=".$search['working_status'];
		}
		if ($search['driver_search']>0){
			$where.=" AND cb.`driver_id`=".$search['driver_search'];
		}
		if ($search['agency_search']>0){
			$where.=" AND cb.`agency_id`=".$search['agency_search'];
		}
		if ($search['customer']>0){
			$where.=" AND cb.`customer_id`=".$search['customer'];
		}
		if ($search['status']>-1){
			$where.=" AND cb.`status`=".$search['status'];
		}
		$order=' ORDER BY cb.`delivey_date`,cb.delivey_time ASC';
		//echo $sql.$where.$order;
		return $db->fetchAll($sql.$where.$order);
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
		return $row = $db->fetchRow($sql);
	}
	function getDriverInformation($driver_id){
		$db = $this->getAdapter();
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$lang= $dbgb->getCurrentLang();
		$arrayview = array(1=>"name_en",2=>"name_kh");
		$sql=" SELECT d.*,
		(SELECT ldc_view.".$arrayview[$lang]." FROM `ldc_view` WHERE ldc_view.type=1 AND key_code =d.`sex` LIMIT 1) AS sexs
		FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!=''
		AND d.id =$driver_id ";
		return $db->fetchRow($sql);
	}
	function checkingCustomer($cus_phone,$cus_email){
		$db = $this->getAdapter();
		//$cus_name = str_replace(' ', '', $cus_name);
		$cus_phone = str_replace(' ', '', $cus_phone);
		$cus_email = str_replace(' ', '', $cus_email);
		$sql="SELECT id,last_name,phone,email FROM ldc_customer 
		       WHERE  STATUS=1
		       AND REPLACE(phone,' ','')='$cus_phone'
		       AND REPLACE(email,' ','')='$cus_email'";
		return $db->fetchRow($sql);
	}
	
	public function addCarBooking($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$client_code = $_db->getNewClientId();
			$cus_id=0;
			$row_cus=$this->checkingCustomer($_data['cus_phone'], $_data['cus_email']);
			if(!empty($row_cus)){
				$cus_id=$row_cus['id'];
			}else{
				$_cus=array(
						'customer_code'=>$client_code,
						'last_name'	  => $_data['cus_name'],
					    'phone'	      => $_data['cus_phone'],
					    'email'	  	  => $_data['cus_email'],
						'status'	  => 1,
						'user_id'     => $this->getUserId(),
						);
				$this->_name="ldc_customer";
				$cus_id=$this->insert($_cus);
			}
			$booking_code = $_db->getNewCarBookingNO();
			$_arrbooking=array(
					'customer_id'	  => $cus_id,
					'note'	  	      => $_data['note'],
					'vehicletype_id'  => $_data['vehicle_type'],
					'agency_id'	  	  => $_data['agency'],
					'booking_no'	  => $booking_code,
					'booking_date'	  => $_data['booking_date'],
					'delivey_date'	  => $_data['delivery_date'],
					'delivey_time'	  => $_data['delivery_time'],
					'fly_no'	  	  => $_data['fly_no'],
					'from_location'	  => $_data['from_location'],
					'to_location'	  => $_data['to_location'],
					'qty'	  		  => 1,
					'price'	  		  => $_data['price'],
					'commision_fee'	  => $_data['commision_fee'],
					'commision_fee_after'=> $_data['commision_fee'],
					'is_paid_commission'=> 0,
					'other_fee'	  		=> $_data['other_fee'],
					'total'	  			=> $_data['total'],
					'due'	  			=> $_data['total'],
					'due_after'	  		=> $_data['total'],
					//'driver_fee'	  	=> $_data['driver_fee'],
					//'driver_fee_after'	=> $_data['driver_fee'],
					'remark'	  		=> $_data['remark'],
					'status_working'	=>0,
					'payment_booking_no'=>$_data['other_booking_no'],
					'grand_total'		=>$_data['total_payment'],
					'grand_total_after'	=>$_data['total_payment'],
					'paid'	  		  	=> $_data['total_paid'],
					'balance'	  	  	=> $_data['balance'],
					'balance_status'	=> $_data['balanc_status'],
					'paid_status'		=> $_data['paid_status'],
					'status'	  		=> 1,
					'is_paid_to_driver'	=> 0,
					'is_customer_paid'	=> 0,
					'create_date'		=> date("Y-m-d H:i:s"),
					'modify_date'  		=>date("Y-m-d H:i:s"),
					'user_id'      		=> $this->getUserId(),
			);
			$this->_name="ldc_carbooking";
			$idbooking = $this->insert($_arrbooking);
			
			if($_data['record_row']!=''){
				$ids=explode(',',$_data['record_row']);
				foreach ($ids as $key => $i)
				{
					$data_item= array(
							'carbooking_id'	=> 	$idbooking,
							'service_id' 	=> 	$_data['service'.$i],
							'total_amount'  =>	$_data['price_'.$i],
							'description'   =>	$_data['note_'.$i],
							'create_date'   =>	date("Y-m-d H:i:s"),
							'user_id'      	=>	$this->getUserId(),
							'status'      	=> 1,
					);
					$this->_name='ldc_booking_service_detial';
					$this->insert($data_item);
				}
			}
			
			if ($_data['total_paid']>0){
				$_data['booking_id'] = $idbooking;
				$this->addCarbookingPayment($_data);
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	public function updateCarBooking($_data){
		$db = $this->getAdapter();
		$_db = new Application_Model_DbTable_DbGlobal();
		$client_code = $_db->getNewClientId();
		$db->beginTransaction();
		try{
			$commission_pay = $this->getTotalCommissionFee($_data['booking_id']);
			$commiss_paid = 0;
			if (!empty($commission_pay)){
				$commiss_paid = $commission_pay['total_paid_commiss'];
			}
			$commision_fee_after = $_data['commision_fee'] - $commiss_paid;
			$is_commissionpaid = 0;
			if ($commision_fee_after<=0){
				$is_commissionpaid =1;
			}
			
			$driverfee_pay = $this->getTotalDriverFee($_data['booking_id']);
			$driverfee_paid = 0;
			if (!empty($driverfee_pay)){
				$driverfee_paid = $driverfee_paid['total_driver_fee'];
			}
			$driver_feeafter = $_data['driver_fee']-$driverfee_paid;
			$is_driverpaid = 0;
			if ($driver_feeafter<=0){
				if ($_data['driver']>0){
					$is_driverpaid =1;
				}
			}
			
			$cus_id=0;
			$row_cus=$this->checkingCustomer($_data['cus_phone'], $_data['cus_email']);
			if(!empty($row_cus)){
				$cus_id=$row_cus['id'];
			}else{
				$_cus=array(
						'customer_code'=>$client_code,
						'last_name'	  => $_data['cus_name'],
						'phone'	      => $_data['cus_phone'],
						'email'	  	  => $_data['cus_email'],
						'status'	  => 1,
						'user_id'     => $this->getUserId(),
				);
				$this->_name="ldc_customer";
				$cus_id=$this->insert($_cus);
			}
			
			$_arrbooking=array(
					'customer_id'	  => $cus_id,
					'note'	  	      => $_data['note'],
					'vehicletype_id'  => $_data['vehicle_type'],
					'agency_id'	  	  => $_data['agency'],
// 					'booking_no'	  => $booking_code,
					'booking_date'	  => $_data['booking_date'],
					'delivey_date'	  => $_data['delivery_date'],
					'delivey_time'	  => $_data['delivery_time'],
					'fly_no'	  	  => $_data['fly_no'],
					'from_location'	  => $_data['from_location'],
					'to_location'	  => $_data['to_location'],
					'qty'	  		  => 1,
					'price'	  		  => $_data['price'],
					'commision_fee'	  => $_data['commision_fee'],
					'commision_fee_after'=> $commision_fee_after,
					'is_paid_commission'=> $is_commissionpaid,
					'other_fee'	  	  => $_data['other_fee'],
					'total'	  		  => $_data['total'],
					'due'	  		  => $_data['total'],
					'due_after'	  	  => $_data['total'],
					//'driver_fee'	  => $_data['driver_fee'],
					//'driver_fee_after'=> $driver_feeafter,
					'remark'	  	  => $_data['remark'],
					'status_working'  =>0,
					'payment_booking_no'=>$_data['other_booking_no'],
					'grand_total'	  =>$_data['total_payment'],
					'grand_total_after'=>$_data['total_payment'],
					'paid'	  		  => $_data['total_paid'],
					'balance'	  	  => $_data['balance'],
					'balance_status'	=> $_data['balanc_status'],
					'paid_status'		=> $_data['paid_status'],
					'status'	  	  => 1,
					'is_paid_to_driver'=> $is_driverpaid,
					'is_customer_paid'=> 0,
					'create_date'	  => date("Y-m-d H:i:s"),
					'modify_date'  	  => date("Y-m-d H:i:s"),
					'user_id'      	  => $this->getUserId(),
			);
			$this->_name="ldc_carbooking";
			$where = " id = ".$_data['booking_id'];
			$this->update($_arrbooking, $where);
			$idbooking = $_data['booking_id'];
			
			$sql = "DELETE FROM ldc_booking_service_detial WHERE carbooking_id=".$idbooking;
			$db->query($sql);
			
			if($_data['record_row']!=''){
			    $ids=explode(',',$_data['record_row']);
				foreach ($ids as $key => $i)
				{
					$data_item= array(
							'carbooking_id'	=> 	$idbooking,
							'service_id' 	=> 	$_data['service'.$i],
							'total_amount'  =>	$_data['price_'.$i],
							'description'   =>	$_data['note_'.$i],
							'create_date'   =>	date("Y-m-d H:i:s"),
							'user_id'      	=>	$this->getUserId(),
							'status'      	=> 1,
					);
					$this->_name='ldc_booking_service_detial';
					$this->insert($data_item);
				}
			}

			$chekcpayment = $this->checkBookingHasPayment($idbooking);
			if (empty($chekcpayment)){
				if ($_data['total_paid']>0){
					$_data['booking_id'] = $idbooking;
					$this->addCarbookingPayment($_data);
				}
			}
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	function getTotalCommissionFee($booking_id){
		$db = $this->getAdapter();
		$sql="
			SELECT 
				SUM(pd.`paid`) AS total_paid_commiss
			FROM `ldc_commission_payment_detail` AS pd,
				`ldc_commission_payment` AS p
			WHERE 
				p.id = pd.`commission_payment_id` AND pd.`booking_id` =$booking_id
				AND p.`status` =1";
		return $db->fetchRow($sql);
	}
	
	function getTotalDriverFee($booking_id){
		$db = $this->getAdapter();
		$sql="
		SELECT 
			SUM(pd.`paid`) AS total_driver_fee
		FROM `ldc_driver_payment_detail` AS pd,
			`ldc_driver_payment` AS p
		WHERE 
			p.id = pd.`driver_payment_id` AND pd.`booking_id` =$booking_id
			AND p.`status` =1";
		return $db->fetchRow($sql);
	}
	function checkBookingHasPayment($booking_id){
		$db = $this->getAdapter();
		$sql="
			SELECT cpd.* 
			FROM `ldc_carbooking_payment_detail` AS cpd,
			`ldc_carbooking_payment` AS cp
			WHERE 
				cp.`id` = cpd.`payment_id`
				AND cpd.`booking_id` = $booking_id
				AND cp.`status`=1";
		return $db->fetchRow($sql);
	}
	function getCarbookingById($id){
		$db = $this->getAdapter();
		$sql="SELECT cb.*,c.last_name,c.phone,c.email
			     FROM `ldc_carbooking` AS cb,ldc_customer AS c
			     WHERE c.id=cb.customer_id AND cb.`id` = $id LIMIT 1";
		return $db->fetchRow($sql);
	}
	
	function addCarbookingPayment($_data){
// 		$db = $this->getAdapter();
// 		$db->beginTransaction();
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$payment_no = $_db->getNewPaymentNO();
			$_arrpayment=array(
					'payment_no'	  => $payment_no,
					'customer_id'	  => $_data['customer'],
					'payment_date'	  => $_data['booking_date'],
					'payment_type'	  => $_data['agency'],
					'payment_method'  => $_data['payment_method'],
					'paid'	  		  => $_data['total_paid'],
					'balance'	  	  => $_data['balance'],
					'grand_total'	  => $_data['total'],
					'note'	  		  => $_data['payment_note'],
					'paid_status'	  => $_data['paid_status'],
					'status'	  	  => 1,
					'create_date'	  => date("Y-m-d H:i:s"),
					'modify_date'  	  =>date("Y-m-d H:i:s"),
					'user_id'      	  => $this->getUserId(),
			);
			$this->_name="ldc_carbooking_payment";
			$idpayment = $this->insert($_arrpayment);
			
			$_arrpaymentdetail=array(
					'payment_id'	  => $idpayment,
					'booking_id'	  => $_data['booking_id'],
					'due_amount'	  => $_data['total'],
					'paid'	  		  => $_data['total_paid'],
					'remain'	  	  => $_data['balance'],
					'paid_from'	  	  => 1,// paid from booking form
			);
			$this->_name="ldc_carbooking_payment_detail";
			$idpayment = $this->insert($_arrpaymentdetail);
			
			$dueafter=0;
			$is_payment =0;
			$bookings = $this->getCarbookingById($_data['booking_id']);
			$paid = $_data['total_paid'];
			if (!empty($bookings)){
				$dueafter =	$bookings['due_after']-$paid;
				if ($dueafter>0){
					$is_payment=0;
				}else{
					$is_payment=1;
				}
				$array=array(
						'is_customer_paid'=>$is_payment,
						'due_after'		  =>$dueafter,
				);
				$this->_name="ldc_carbooking";
				$where = " id=".$_data['booking_id'];
				$this->update($array, $where);
			}
// 			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
// 			$db->rollBack();
		}
	}
	
	function addDrivert($_data){
		try{
			$_db = new Application_Model_DbTable_DbGlobal();
			$payment_no = $_db->getNewPaymentNO();
			$arr=array(
					'status_working'  => $_data['working_status'],
					'driver_id'	  	  => $_data['driver'],
					'vehicle_id'	  => $_data['vehicle'],
					
					'delivey_time'	  => $_data['delivery_time'],
					'driver_fee'	  => $_data['driver_fee'],
					'driver_fee_after'=> $_data['driver_fee'],
					'remark'	  	  => $_data['remark'],
					'modify_date'  	  =>date("Y-m-d H:i:s"),
					'user_id'      	  => $this->getUserId(),
			);
			$this->_name="ldc_carbooking";
			$where=" id=".$_data['id'];
			$this->update($arr, $where);
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			//$db->rollBack();
		}
	}
	
	public function getAllServiceType(){
		$db=$this->getAdapter();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$sql =" SELECT id,service_title AS `name` FROM ldc_booking_service WHERE service_title!='' AND `status`=1";
		$rows=$db->fetchAll($sql);
		$options = '';
		$options .= '<option value="0">'.$tr->translate("SELECT_SERVICE").'</option>';
		$options .= '<option value="-1">'.$tr->translate("ADD_NEW").'</option>';
		if(!empty($rows))foreach($rows as $value){
			$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['name']).'</option>';
		}
		return $options;
	}
	
	public function getAllServiceoption(){
		$db=$this->getAdapter();
		$sql =" SELECT id,service_title AS `name` FROM ldc_booking_service WHERE service_title!='' AND `status`=1";
		return $db->fetchAll($sql);
	}
	
	public function getServiceDetail($id){
		$db=$this->getAdapter();
		$sql="SELECT id,service_id,carbooking_id,total_amount,description 
		       FROM ldc_booking_service_detial
		       WHERE carbooking_id=$id";
		return $db->fetchAll($sql);
	}
	
	function getVehicleByCategory(){
		$db = $this->getAdapter();
		$sql='SELECT v.id,
		CONCAT(m.`title`," ",mo.`title`," ",smo.`title`," (",v.`reffer`,")") AS `name`
		FROM `ldc_vehicle` AS v,
		`ldc_make` AS m,
		`ldc_model` AS mo,
		`ldc_submodel` AS smo
		WHERE
		v.`make_id` = m.`id` AND
		v.`model_id` = mo.`id` AND
		v.`sub_model` = smo.`id` AND
		v.is_sale !=1
		AND v.`status`=1
		';
		return $db->fetchAll($sql);
	}
	
	function getVehicleByCarType($cat_id){
		$db = $this->getAdapter();
		$sql='SELECT v.id,
		CONCAT(m.`title`," ",mo.`title`," ",smo.`title`," (",v.`reffer`,")") AS `name`
		FROM `ldc_vehicle` AS v,
		`ldc_make` AS m,
		`ldc_model` AS mo,
		`ldc_submodel` AS smo
		WHERE
		v.`make_id` = m.`id` AND
		v.`model_id` = mo.`id` AND
		v.`sub_model` = smo.`id` AND
		v.is_sale !=1
		AND v.`status`=1
		AND v.car_type='.$cat_id;
		return $db->fetchAll($sql);
	}
	
	function checkBookNo($book_no){
		$db = $this->getAdapter();
		$book_no = str_replace(' ', '', $book_no);
		$sql=" SELECT cb.id,cb.payment_booking_no AS book_no FROM ldc_carbooking AS cb WHERE REPLACE(cb.payment_booking_no,' ','')='$book_no'";
	    return $db->fetchRow($sql);
	}
}
?>