<?php

class Application_Model_DbTable_DbGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('authcar');
		return $session_user->user_id;
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		if(!empty($session_lang->lang_id)){
			if ($session_lang->lang_id>2){
				return 2;
			}
			return $session_lang->lang_id;
		}else{
			return 2;
		}
	}
	public function getNewClientId(){
		$this->_name='ldc_customer';
		$db = $this->getAdapter();
		$row = $this->getSystemSetting('customer_prefix');
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = ($row['value']);
		for($i = $acc_no;$i<4;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	public function getNewAgencyId(){
		$this->_name='ldc_agency';
		$db = $this->getAdapter();
		$row = $this->getSystemSetting('customer_prefix');
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = ($row['value']);
		for($i = $acc_no;$i<4;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
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
	public function getAllPackageDay($opt=null){
		$this->_name='ldc_rankday';
		$sql = " SELECT id,CONCAT(day_title,'(',from_amountday,'-',to_amountday,')') as package FROM $this->_name WHERE status=1 AND day_title!='' ";
		$db = $this->getAdapter();
		$rows =$db->fetchAll($sql);
		if($opt!=null){
			$options="";
			if(!empty($rows))foreach($rows AS $row){
				$options[$row['id']]=$row['day_title'];
			}
			return $options;
		}else{
			return $rows;
		}
	}
	public function getAllTax($opt=null){
		$this->_name='ldc_tax';
		$sql = " SELECT value as id ,CONCAT(title,'( ',value,'%)') AS title FROM $this->_name WHERE title!='' AND STATUS=1 ";
		$db = $this->getAdapter();
		$rows =$db->fetchAll($sql);
		if($opt!=null){
			$options="";
			if(!empty($rows))foreach($rows AS $row){
				$options[$row['id']]=$row['title'];
			}
			return $options;
		}else{
			return $rows;
		}
	}
	function getAllCountry(){
		$sql="SELECT * FROM ldc_country WHERE status=1 AND country_name!='' ";
		$db = $this->getAdapter();
		return $db->fetchAll($sql);
	}
	function getAllMake(){
		$db = $this->getAdapter();
		$sql = " SELECT id ,title AS name FROM ldc_make WHERE status = 1";
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$order);
	}
	function getAllModels(){
		$db = $this->getAdapter();
		$sql = " SELECT id,title AS name FROM ldc_model WHERE status = 1";
		return  $db->fetchAll($sql);
	}
	function ajaxaddMake($data){
		$this->_name='ldc_make';
		$db = $this->getAdapter();
		$arr = array(
				'title'=>$data['txt_make'],
				'status'=>1
	
		);
		return $this->insert($arr);
	}
	function ajaxaddModel($data){
		$this->_name='ldc_model';
		$db = $this->getAdapter();
		$arr = array(
				'brand_id'=>$data['txt_makeid'],
				'title'=>$data['txt_model'],
				'status'=>1
	
		);
		return $this->insert($arr);
	}
	function getViews($type=2){
		$db=$this->getAdapter();
		$lang= $this->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT key_code,".$array[$lang]." as name_en FROM ldc_view WHERE `type`=$type";
		return $db->fetchAll($sql);
	}
	function getViewsAsName($type=2){
		$db=$this->getAdapter();
		$lang= $this->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT key_code as id ,".$array[$lang]." as name FROM ldc_view WHERE `type`=$type";
		return $db->fetchAll($sql);
	}
	public  function getclientdtype(){
		$db = $this->getAdapter();
		$lang= $this->getCurrentLang();
		$array = array(1=>"name_en",2=>"name_kh");
		$sql="SELECT key_code AS id,".$array[$lang]." AS name ,displayby FROM `ldc_view` WHERE STATUS =1 AND type=6";
		$rows = $db->fetchAll($sql);
		return $rows;
	}
	public function getNewBookingCode(){
		$this->_name='ldc_booking';
		$db = $this->getAdapter();
		$row = $this->getSystemSetting('booking_prefix');
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = ($row['value']);
		for($i = $acc_no;$i<3;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	public function getDriverCode(){
		$this->_name='ldc_driver';
		$db = $this->getAdapter();
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
	
		$row = $this->getSystemSetting('driver_prefix');
		$pre = ($row['value']);
		for($i = $acc_no;$i<3;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	public function getVehiclePrice($day,$vehicle_id){
		$db = $this->getAdapter();
		$sql="SELECT v.`price`,v.`extraprice`,v.`vat_value`  FROM `ldc_vehiclefee_detail` AS v WHERE v.`packageday_id`=(SELECT r.id FROM `ldc_rankday` AS r WHERE r.`from_amountday`<=$day AND r.`to_amountday`>=$day LIMIT 1) AND v.`vehicle_id` = $vehicle_id";
		$row = $db->fetchRow($sql);
		if(empty($row)){
			$sql="SELECT v.`price`,v.`extraprice`,v.`vat_value`  FROM `ldc_vehiclefee_detail` AS v WHERE v.`packageday_id`=(SELECT r.id FROM `ldc_rankday` AS r WHERE r.`is_morethen`=1 LIMIT 1) AND v.`vehicle_id` = $vehicle_id";
			return $db->fetchRow($sql);
		}else{
			return $row;
		}
	}
	public function getAllAvailableVehicle($data){
		$db = $this->getAdapter();
		$pickupdate = date("Y-m-d",strtotime($data["pickup_date"]));
		$returndate = date("Y-m-d",strtotime($data["return_date"]));
		 
// 		$pickuptime = $data["pickup_time"];
		$returntime = $data["return_time"];
// 		$sql = "SELECT v.id,v.`reffer`,v.`frame_no`,v.`max_weight`,
// 		v.`seat_amount`,v.`color`,v.`year`,v.`steering`,v.`test_url`,v.`show_url`,
// 		v.`img_front`,
// 		v.`img_front_right`,v.img_seat,
// 		v.`is_promotion`,v.`discount`,(SELECT m.title FROM `ldc_make` AS m WHERE m.id=v.`make_id`)
// 		AS make,(SELECT md.title FROM `ldc_model` AS md WHERE md.id=v.`model_id`) AS model,
// 		(SELECT sm.title FROM `ldc_submodel` AS sm WHERE sm.id=v.`sub_model`) AS sub_model,
// 		(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
// 		(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
// 		(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine` FROM `ldc_vehicle` AS v WHERE v.is_sale !=1 AND v.status=1 AND v.id
// 		NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND '$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date` AND '$returndate ' BETWEEN b.`pickup_date` AND b.`return_date` AND bd.item_type=1 AND b.status!=3)"; // it wiil include with new version AND b.`return_time` >= '$returntime'
		
		$sql = "SELECT v.id,v.`reffer`,v.`frame_no`,v.`max_weight`,
		v.`seat_amount`,v.`color`,v.`year`,v.`steering`,v.`test_url`,v.`show_url`,
		v.`img_front`,
		v.`img_front_right`,v.img_seat,
		v.`is_promotion`,v.`discount`,(SELECT m.title FROM `ldc_make` AS m WHERE m.id=v.`make_id`)
		AS make,(SELECT md.title FROM `ldc_model` AS md WHERE md.id=v.`model_id`) AS model,
		(SELECT sm.title FROM `ldc_submodel` AS sm WHERE sm.id=v.`sub_model`) AS sub_model,
		(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission,
		(SELECT vt.`title` FROM `ldc_vechicletye` AS vt WHERE vt.id=v.`car_type` LIMIT 1) AS `type`,
		(SELECT e.`capacity` FROM `ldc_engince` AS e WHERE e.id=v.`engine`) AS `engine` FROM `ldc_vehicle` AS v WHERE v.is_sale !=1 AND v.status=1 AND v.id
		NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND
		('$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date`
				OR '$returndate ' BETWEEN b.`pickup_date` AND b.`return_date`)
				AND bd.item_type=1 AND b.status!=3)"; // it wiil include with new version AND b.`return_time` >= '$returntime'
		$row = $db->fetchAll($sql);
		if(!empty($row)){
			return $db->fetchAll($sql);
		}
	}
	public function getAvailableDriver($data){
		$db= $this->getAdapter();
		$pickup_date = new DateTime($data["pickup_date"]);
		$return_date = new DateTime($data["return_date"]);
		 
		$pickupdate = $pickup_date->format('Y-m-d'); // 2003-10-16
		$returndate = $return_date->format('Y-m-d');
		 
		$returntime = $data["return_time"];
		$sql="SELECT d.`id`,d.`driver_id`,CONCAT(d.`first_name`,' ',d.`last_name`) AS `name`,d.`experience_desc`,d.`sex`,d.`nationality`,d.`lang_note`,d.`tel`,d.`email`,d.`photo`,d.`c_holidayprice`,d.`c_normalprice`,d.`c_otprice`,d.`c_weekendprice`,d.`p_holidayprice`,d.`p_normalprice`,d.`p_otprice`,d.`p_weekendprice`
		FROM `ldc_driver` AS d WHERE d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND '$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date` AND '$returndate ' BETWEEN b.`pickup_date` AND b.`return_date` AND bd.item_type=2 AND b.status!=3) AND d.`status`=1"; // AND b.`return_time` >= '$returntime'
		return $db->fetchAll($sql);
	}
	public function geVehicleById($vehicle_id){
		$db = $this->getAdapter();
		$sql = "SELECT
		v.id,v.`reffer`,v.`frame_no`,v.ordering,
		licence_plate,max_weight,seat_amount,org_cost,chassis_no,
		(SELECT e.capacity FROM `ldc_engince` AS e WHERE e.id=v.engine) AS engine
		,transmission,
		(SELECT type FROM `ldc_type` WHERE id = v.type AND STATUS=1) AS type,
		(SELECT t.title FROM `ldc_vechicletye` AS t WHERE t.id=v.car_type) AS car_type,
		steering,test_url,show_url,description,
		img_front_left,img_front_right,img_rear_left,img_rear_right,img_passenger,img_trunk,img_seat,
		img_sr,img_luggage,img_sl,img_sr,img_rear,img_front,engine_number,
		v.`color`,v.`year`,v.`steering`,of_axlex,of_cylinder,cylinders_dip,
		(SELECT m.title FROM `ldc_make` AS m WHERE m.id=v.`make_id`) AS make,
		(SELECT md.title FROM `ldc_model` AS md WHERE md.id=v.`model_id`) AS model,
		(SELECT sm.title FROM `ldc_submodel` AS sm WHERE sm.id=v.`sub_model`) AS sub_model,
		(SELECT t.`tran_name` FROM `ldc_transmission` AS t WHERE t.`id`=v.`transmission`) AS transmission
		FROM `ldc_vehicle` AS v WHERE v.id =$vehicle_id LIMIT 1";
		return $row = $db->fetchRow($sql);
	}
	static function getVehiceRankingDay($vehicle_id){
		$sql ="SELECT (SELECT CONCAT(from_amountday,'-',to_amountday,' Day') FROM `ldc_rankday` WHERE id=packageday_id) AS package_name,price
		FROM `ldc_vehiclefee_detail` AS v ,ldc_rankday AS r WHERE v.vehicle_id=$vehicle_id AND v.status=1 AND r.id=v.packageday_id ORDER BY r.ordering ASC";
		$db = new Application_Model_DbTable_DbGlobal();
		return $db->getGlobalDb($sql);
	}
	public function getEquipment($data){
		$db= $this->getAdapter();
		$pickupdate = date("Y-m-d",strtotime($data["pickup_date"]));
		$returndate = date("Y-m-d",strtotime($data["return_date"]));
		$returntime = $data["return_time"];
		 
		$pickupdates=date_create($data["pickup_date"]);
		$returndates =date_create($data["return_date"]);
		$diff=date_diff($pickupdates,$returndates);
		$day = $diff->format("%a%")+1;
		$sql_rankday = "SELECT r.`id` FROM `ldc_rankday` AS r WHERE r.`from_amountday`<=$day AND r.`to_amountday`>=$day LIMIT 1";
		$row_randay = $db->fetchOne($sql_rankday);
		if($row_randay){
			$row_randay;
		}else{
			$sql_rankday = "SELECT r.`id` FROM `ldc_rankday` AS r WHERE r.`is_morethen`=1 LIMIT 1";
			$row_randay=$db->fetchOne($sql_rankday);
		}
		$sql="SELECT d.`id`,d.`equipment_name`,d.`reference_no`,d.`photo_front`,d.`url`,(SELECT st.price FROM `ldc_stuff_details` AS st WHERE st.`stuff_id`=d.`id` AND st.`package_id`=$row_randay limit 1) AS price,(SELECT st.`extra_price` FROM `ldc_stuff_details` AS st WHERE st.`stuff_id`=d.`id` AND st.`package_id`=$row_randay limit 1) AS extra_price  FROM `ldc_stuff` AS d WHERE d.`status`=1";
// 		$sql="SELECT d.`id`,d.`equipment_name`,d.`reference_no`,d.`photo_front`,d.`url`,(SELECT st.price FROM `ldc_stuff_details` AS st WHERE st.`stuff_id`=d.`id` AND st.`package_id`=$row_randay limit 1) AS price,(SELECT st.`extra_price` FROM `ldc_stuff_details` AS st WHERE st.`stuff_id`=d.`id` AND st.`package_id`=$row_randay limit 1) AS extra_price  FROM `ldc_stuff` AS d WHERE d.`status`=1
// 		AND d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND
// 		('$pickupdate' BETWEEN b.`pickup_date` AND b.`return_date`
// 				OR '$returndate' BETWEEN b.`pickup_date` AND b.`return_date`)
// 				AND bd.item_type=3 AND b.status!=3)";
		$row = $db->fetchAll($sql);
		if (!empty($row)) {
			return $row;
		}
		return "";
	}
	public function getAllAvailableGuide($data,$type=3){ // 1=driver,2=guide,3=both
		$db= $this->getAdapter();
		$pickupdate = date("Y-m-d",strtotime($data["pickup_date"]));
		$returndate = date("Y-m-d",strtotime($data["return_date"]));
		
		$returntime = $data["return_time"];
		if($type==1){
			$position_type = " AND d.`position_type`=1";
		}elseif ($type==2){
			$position_type = " AND d.`position_type`=2";
		}else {
			$position_type = "";
		}
		$sql="SELECT d.`id`,d.`driver_id`,CONCAT(d.`first_name`,' ',d.`last_name`) AS `name`,d.`experience_desc`,d.`sex`,d.`nationality`,d.`lang_note`,d.`tel`,d.`email`,d.`photo`,d.`c_holidayprice`,d.`c_normalprice`,d.`c_otprice`,d.`c_weekendprice`,d.`p_holidayprice`,d.`p_normalprice`,d.`p_otprice`,d.`p_weekendprice`,d.`monthly_price`,d.`position_type`,
		(SELECT lv.name_en FROM `ldc_view` AS lv WHERE lv.key_code=d.`position_type` AND lv.type =8 LIMIT 1) AS position_type_title
		FROM `ldc_driver` AS d WHERE  d.id NOT IN(SELECT bd.`item_id` FROM ldc_booking AS b , `ldc_booking_detail` AS bd WHERE b.id=bd.`book_id` AND b.`return_date` BETWEEN '$pickupdate' AND '$returndate'  AND bd.item_type=2 AND b.status !=3) AND d.`status`=1 $position_type"; // Will Include with new Version AND b.`return_time` >= '$returntime'
		$row = $db->fetchAll($sql);
		if (!empty($row)) {
			return $row;
		}
		return "";
	}
	public function getAllNameOwner($opt=null){
		$sql="SELECT id,owner_name FROM ldc_owner WHERE 1";
		return $this->getAdapter()->fetchAll($sql);
	}
	function getOwnerById($id){
		$db = $this->getAdapter();
		$sql = "SELECT id,owner_name,`position`,id_card,hand_phone,email,hotline,`status` FROM ldc_owner where id= ".$id;
		return $db->fetchRow($sql);
	}
	public static function GlobalgetUserId(){
		$session_user=new Zend_Session_Namespace('authcar');
		return $session_user->user_id;
	}
	public function getAccessPermission($branch_str='branch_id'){
		$session_user=new Zend_Session_Namespace('authcar');
		$branch_id = $session_user->branch_id;
		$level = $session_user->level;
		if($level==1 OR $level==2){
			$result = "";
			return '';
		}
		else{
			$result = " AND $branch_str =".$branch_id;
			return '';
		}
	}
	
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	function getCurrentDatePayment($id){
		$db = $this->getAdapter();
		$sql="SELECT c.`date_input` FROM `ln_client_receipt_money` AS c WHERE c.`id`=$id ";
		return $db->fetchOne($sql);
	}
	function getLastDatePayment($id){
		$db = $this->getAdapter();
		$sql="SELECT crm.`date_input` FROM `ln_client_receipt_money` AS crm,`ln_client_receipt_money_detail` AS crmd WHERE crm.`id`!=$id AND crm.`id`=(SELECT crl.`crm_id` FROM `ln_client_receipt_money_detail` AS crl WHERE crl.`crm_id`=crm.`id` AND crl.`loan_number`=(SELECT c.loan_number FROM `ln_client_receipt_money_detail` AS c WHERE c.`crm_id`=crmd.id AND c.`crm_id`=$id LIMIT 1) LIMIT 1)  ORDER BY crm.`date_input` DESC LIMIT 1 ";
		return $db->fetchOne($sql);
	}
	
	public function getGlobalDb($sql)
  	{
  		$db=$this->getAdapter();
  		$row=$db->fetchAll($sql);  		
  		if(!$row) return NULL;
  		return $row;
  	}
  	public function getGlobalDbRow($sql)
  	{
  		$db=$this->getAdapter();  		
  		$row=$db->fetchRow($sql);
  		if(!$row) return NULL;
  		return $row;
  	}
  	public static function getActionAccess($action)
    {
    	$arr=explode('-', $action);
    	return $arr[0];    	
    }     
    public function isRecordExist($conditions,$tbl_name){
		$db=$this->getAdapter();		
		$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1"; 
		$row= count($db->fetchRow($sql));
		if(!$row) return NULL;
		return $row;	
    }
    /*for select 1 record by id of earch table by using params*/
    public function GetRecordByID($conditions,$tbl_name){
    	$db=$this->getAdapter();
    	$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1";
    	$row = $this->fetchRow($sql);
    	return $row;
    	$row= $db->fetchRow($sql);
    	return $row;
    }
    /**
     * insert record to table $tbl_name
     * @param array $data
     * @param string $tbl_name
     */
    public function addRecord($data,$tbl_name){
    	$this->setName($tbl_name);
    	return $this->insert($data);
    }
    public function updateRecord($data,$id,$tbl_name){
    	$this->setName($tbl_name);
    	$where=$this->getAdapter()->quoteInto('id=?',$id);
    	$this->update($data,$where);    	
    }   
    public function DeleteRecord($tbl_name,$id){
    	$db = $this->getAdapter();
		$sql = "UPDATE ".$tbl_name." SET status=0 WHERE id=".$id;
		return $db->query($sql);
    } 
     public function DeleteData($tbl_name,$where){
    	$db = $this->getAdapter();
		$sql = "DELETE FROM ".$tbl_name.$where;
		return $db->query($sql);
    } 
    public function getDayInkhmerBystr($str){
    	
    	$rs=array(
    			'Mon'=>'ច័ន្ទ',
    			'Tue'=>'អង្គារ',
    			'Wed'=>'ពុធ',
    			'Thu'=>"ព្រហ",
    			'Fri'=>"សុក្រ",
    			'Sat'=>"សៅរី",
    			'Sun'=>"អាទិត្យ");
    	if($str==null){
    		return $rs;
    	}else{
    	return $rs[$str];
    	}
    
    }
    public function convertStringToDate($date, $format = "Y-m-d H:i:s")
    {
    	if(empty($date)) return NULL;
    	$time = strtotime($date);
    	return date($format, $time);
    }   
    public static function getResultWarning(){
          return array('err'=>1,'msg'=>'មិន​ទាន់​មាន​ទន្និន័យ​នូវ​ឡើយ​ទេ!');	
    }
   /*@author Mok Channy
    * for use session navigetor 
    * */
//    public static function SessionNavigetor($name_space,$array=null){
//    	$session_name = new Zend_Session_Namespace($name_space);
//    	return $session_name;   	
//    }
   public function getAllProvince($opt=null){
   	$this->_name='ldc_province';
   	$lang= $this->getCurrentLang();
   	$array = array(1=>"province_en_name",2=>"province_kh_name");
   	$sql = " SELECT id,".$array[$lang]." as  name FROM $this->_name WHERE status=1 AND province_en_name!='' ORDER BY ".$array[$lang]." ASC";
   	$db = $this->getAdapter();
   	$rows =$db->fetchAll($sql);
   	if($opt!=null){
   		$options="";
   		if(!empty($rows))foreach($rows AS $row){
   			$options[$row['id']]=$row['name'];
   		}
   		return $options;
   	}else{
   		return $rows;
   	}
   }
   public function getServiceType($opt=null){
   	$this->_name='ldc_service_type';
   	$lang= $this->getCurrentLang();
   	$array = array(1=>"title_en",2=>"title_kh");
   	$sql = " SELECT id,".$array[$lang]." AS  name FROM $this->_name WHERE status=1 AND title_en!='' ORDER BY id DESC";
   	$db = $this->getAdapter();
   	$rows =$db->fetchAll($sql);
   	if($opt!=null){
   		$options="";
   		if(!empty($rows))foreach($rows AS $row){
   			$options[$row['id']]=$row['name'];
   		}
   		return $options;
   	}else{
   		return $rows;
   	}
   }
   function getAllLocationType(){
   	$db = $this->getAdapter();
   	$this->_name='ldc_locationtype';
   	$lang= $this->getCurrentLang();
   	$array = array(1=>"title",2=>"title_kh");
   	$sql = " SELECT id ,title as name FROM $this->_name WHERE status=1 AND title!=''  ";
   	$order=" order by id DESC";
   	return $db->fetchAll($sql.$order);
   }
   
   function getAllLocationByProvince($province,$opt=null){
   	$db = $this->getAdapter();
   	$sql = "SELECT id ,location_name as name FROM `ldc_package_location` WHERE STATUS=1 AND province_id=$province AND is_package!=1 ";
   	$row =  $db->fetchAll($sql);
   	if($opt!=null){
   		$option='';
   		foreach($row as $r){
   			$option .= '<option value="'.$r['id'].'">'.htmlspecialchars($r['location_name'], ENT_QUOTES).'</option>';
   		}
   		return $option;
   	}else {
   		return $row;
   	}
   }
   
   public function getAllDistrict(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id,pro_id,CONCAT(district_name,'-',district_namekh) district_name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getAllDistricts(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id AS id,pro_id,CONCAT(district_name,'-',district_namekh) name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getCommune(){
   	$this->_name='ln_commune';
   	$sql = " SELECT com_id,com_id AS id,commune_name,CONCAT(commune_name,'-',commune_namekh) AS name,district_id FROM $this->_name WHERE status=1 AND commune_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }

   
   public function getVillage(){
   	$this->_name='ln_village';
   	$sql = " SELECT vill_id,vill_id AS id,village_name,CONCAT(village_namekh,'-',village_name) AS name,commune_id FROM $this->_name WHERE status=1 AND village_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
  
   public function getAllCurrency($id,$opt = null){
	   	$sql = "SELECT * FROM ln_currency WHERE status = 1 ";
	   	if($id!=null){
	   		$sql.=" AND id = $id";
	   	}
	   	$rows = $this->getAdapter()->fetchAll($sql);
	   	if($opt!=null){
	   		$options="";
	   		if(!empty($rows))foreach($rows AS $row){
	   			$options[$row['id']]=($row['displayby']==1)?$row['displayby']:$row['curr_nameen'];
	   		}
	   		return $options;
	   	}else{
	   		return $rows;
	   	}
   	
   }
   
   
  
   
   public static function getCurrencyType($curr_type){
   	$curr_option = array(
   			1=>'រៀល',
   			2=>'ដុល្លា'
   			);
   	return $curr_option[$curr_type];
   	
   }
   public function getAllSituation($id = null){
   	$_status = array(
   			1=>$this->tr->translate("Single"),
   			2=>$this->tr->translate("Married"),
   			3=>$this->tr->translate("Windowed"),
   			4=>$this->tr->translate("Mindowed")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
   public function GetAllIDType($id = null){
   	$_status = array(
   			1=>$this->tr->translate("National ID"),
   			2=>$this->tr->translate("Family Book"),
   			3=>$this->tr->translate("Resident Book"),
   			4=>$this->tr->translate("Other")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
 
  function countDaysByDate($start,$end){
  	$first_date = strtotime($start);
  	$second_date = strtotime($end);
  	$offset = $second_date-$first_date;
  	return floor($offset/60/60/24);
  
  }

 public function returnAfterHoliday($holiday_option,$date){
	  $rs = $this->checkHolidayExist($holiday_option,$date);
	  if(is_array($rs)){
	  	$d = new DateTime($rs['start_date']);
	  	$d->modify( 'next day' );//here check for holiday_option
	  	$date =  $d->format( 'Y-m-d' );
	  	$this->returnAfterHoliday($holiday_option,$date);
	  }else{
	  	echo $date;
	  	return $date;
	  }
  }
  public function getVewOptoinTypeByType($type=null,$option = null,$limit =null,$first_option =null){
  	$db = $this->getAdapter();
  	$lang = $this->getCurrentLang();
  	$array = array(1=>"name_en",2=>"name_kh");
  	$sql="SELECT id,key_code,".$array[$lang]." AS name_en ,displayby FROM `ldc_view` WHERE status =1 AND name_en!='' ";//just concate
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
  		$options=array();
  		if($first_option==null){//if don't want to get first select
  			$options=array(''=>"-----ជ្រើសរើស-----",-1=>"Add New",);
  		}
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['key_code']]=$row['name_en'];//($row['displayby']==1)?$row['name_kh']:$row['name_en'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  
  
  function checkEndOfMonth($default_day,$next_payment,$str_next){//default = 31 ,
  	if($str_next=='+1 month'){
  		$str_next='-1 month';
  	}else if($str_next=='+1 week'){
  		$str_next='-1 week';
  	}else{
  		$str_next='-1 day';
  	}
  	
  	$next_payment = date("Y-m-d", strtotime("$next_payment $str_next"));
  	$m = (integer) date('m',strtotime($next_payment));
  	$end_date   = date('Y-m-d',mktime(1,1,1,++$m,0,date('Y',strtotime($next_payment))));
  	return $end_date;
  	
  }
  public function getNextDateById($pay_term,$amount_next_day){
  	if($pay_term==3){
  		$str_next = '+1 month';
  	}elseif($pay_term==2){
  		$str_next = '+1 week';
  	}else{
  		$str_next = '+1 day';
  	}
  	return $str_next;
  }
  public function CountDayByDate($start,$end){
  	//$db = new Application_Model_DbTable_DbGlobal();
	$date = $this->countDaysByDate($start,$end);
  	return $date;
  }
  public function CurruncyTypeOption(){
  	$db = $this->getAdapter();
  	$rows=array(2=>"ដុល្លា",3=>"បាត",1=>"រៀល");
  	$option='';
  	if(!empty($rows))foreach($rows as $key=>$value){
  		$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
  	}
  	return $option;
  }
  public function getSystemSetting($keycode){
  	$db = $this->getAdapter();
  	$sql = "SELECT * FROM `ln_system_setting` WHERE keycode ='".$keycode."'";
//   	echo $sql;
  	return $db->fetchRow($sql);
  }
  static function getPaymentTermById($id=null){
  	$arr = array(
  			1=>"ថ្ងៃ",
  			2=>"អាទិត្យ",
  			3=>"ខែ");
  	if($id!=null){
		return $arr[$id];
  	}
  	return $arr;
  	
  }
  
  function getAllViewType($opt=null,$filter=null){
  		$db = $this->getAdapter();
  	$sql ="SELECT * FROM `ln_view_type` WHERE status=1";
  	if($filter!=null){
  		$sql.=" AND id=12 OR id=13";
  	}
  	$result = $db->fetchAll($sql);
  	$options=array('-1'=>"------Select View Type------");
  	if($opt!=null){
  		if(!empty($result))foreach($result AS $row){
  			    $options[$row['id']]=$row['name'];
  		}
  		return $options;
  	}else{
  		return $result;
  	}
  }
  /* New function  start from 2018-Jan-18*/
  
  public function getVewOptoinTypeByTypes($type=null,$limit =null){
  	$db = $this->getAdapter();
  	$lang = $this->getCurrentLang();
  	$array = array(1=>"name_en",2=>"name_kh");
  	$sql="SELECT key_code as id,".$array[$lang]." AS name ,displayby FROM `ldc_view` WHERE status =1 AND name_en!='' ";//just concate
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	return $rows;
  }
  function getVehicleAvailableList($vehicle_id=null){
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
  	if (!empty($vehicle_id)){
  		$sql.=" AND (v.`id` NOT IN (SELECT d.`vehicle_id` FROM `ldc_driver` AS d WHERE d.`status`=1) OR v.`id`=$vehicle_id)";
  	}else{
  		$sql.=" AND v.`id` NOT IN (SELECT d.`vehicle_id` FROM `ldc_driver` AS d WHERE d.`status`=1)";
  	}
  	return $db->fetchAll($sql);
  }
  function getVehicleHasDriver(){
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
  	$sql.=" AND v.`id` IN (SELECT d.`vehicle_id` FROM `ldc_driver` AS d WHERE d.`status`=1)";
  	return $db->fetchAll($sql);
  }
  public function getNewCarBookingNO(){
  	$this->_name='ldc_carbooking';
  	$db = $this->getAdapter();
  	$row = $this->getSystemSetting('booking_prefix');
  	$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	$pre = ($row['value']);
  	for($i = $acc_no;$i<4;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  public function getNewPaymentNO(){
  	$this->_name='ldc_carbooking_payment';
  	$db = $this->getAdapter();
  	$row = $this->getSystemSetting('paymentprefix');
  	$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	$pre = ($row['value']);
  	for($i = $acc_no;$i<4;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  public function getNewCommissionPaymentNO(){
  	$this->_name='ldc_commission_payment';
  	$db = $this->getAdapter();
  	$row = $this->getSystemSetting('agentpaymentlprefix');
  	$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	$pre = ($row['value']);
  	for($i = $acc_no;$i<4;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  public function getNewCarrentalNO(){
  	$this->_name='ldc_carrental_detail';
  	$db = $this->getAdapter();
  	$row = $this->getSystemSetting('carrentalprefix');
  	$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	$pre = ($row['value']);
  	for($i = $acc_no;$i<4;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  function getAllLocation(){
  	$db = $this->getAdapter();
  	$sql = "SELECT id ,location_name as name FROM `ldc_package_location` WHERE status=1 ORDER BY location_name ASC";
  	$row =  $db->fetchAll($sql);
  	return $row;
  }
  function getAllCustomers(){
  	$sql="SELECT c.id,CONCAT(first_name,' ',last_name,'(',c.`customer_code`,')') AS `name` FROM ldc_customer AS c WHERE c.`status`=1 AND c.`first_name` !='' ORDER BY c.`first_name` ASC";
  	return $this->getAdapter()->fetchAll($sql);
  }
  
  public function getAllDriver(){
  	$db= $this->getAdapter();
  	$sql="SELECT d.`id`,CONCAT(d.`first_name`,' ',d.`last_name`,'(',d.`driver_id`,')') AS `name`
 	FROM `ldc_driver` AS d WHERE d.`status` =1 AND d.`first_name`!='' ORDER BY d.`first_name` ASC";
  	return $db->fetchAll($sql);
  }
  public function getAllAgency(){
  	$sql="SELECT c.id,CONCAT(first_name,' ',last_name,'(',c.`customer_code`,')') AS `name` FROM ldc_agency AS c WHERE c.`status`=1 AND c.`first_name` !='' ORDER BY c.`first_name` ASC";
  	return $this->getAdapter()->fetchAll($sql);
  }
}
?>