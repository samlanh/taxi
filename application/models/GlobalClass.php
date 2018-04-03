<?php

class Application_Model_GlobalClass  extends Zend_Db_Table_Abstract
{
   public static function getInvoiceNo(){	
		//return strtoupper(uniqid());
		$sub=substr(uniqid(rand(10,1000),false),rand(0,10),5);
		$date= new Zend_Date();
		$head="W".$date->get('YY-MM-d/ss');
		return $head.$sub;
   }
   public function getAllProvinceOption(){
   	$_db = new Application_Model_DbTable_DbGlobal();
   	$rows = $_db->getAllProvince();
   	$options= '<option value="-1" >'.htmlspecialchars('Please Select Province', ENT_QUOTES).'</option>';
   	if(!empty($rows))foreach($rows as $value){
   		$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['name'], ENT_QUOTES).'</option>';
   	}
   	return $options;
   }
   public function getOptonsHtml($sql, $display, $value){
   	$db = $this->getAdapter();
   	$option = '<option value="">--- Select ---</option>';
   	foreach($db->fetchAll($sql) as $r){
   			
   		$option .= '<option value="'.$r[$value].'">'.htmlspecialchars($r[$display], ENT_QUOTES).'</option>';
   	}
   	return $option;
   }
   public function getAllPackageDayOption(){
   	$_db = new Application_Model_DbTable_DbGlobal();
   	$rows = $_db->getAllPackageDay();
   	$options= '';
   	if(!empty($rows))foreach($rows as $value){
   		$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['package'], ENT_QUOTES).'</option>';
   	}
   	return $options;
   }
   public function getAllLocationOption(){
   	$sql="SELECT id,province_id,location_name FROM ldc_package_location WHERE `status`=1";
   	$rows=$this->getAdapter()->fetchAll($sql);
   	$options= '<option value="-1" >'.htmlspecialchars('Please Select Province', ENT_QUOTES).'</option>';
   	if(!empty($rows))foreach($rows as $value){
   		$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['location_name'], ENT_QUOTES).'</option>';
   	}
   
   	return $options;
   }
   public function getYesNoOption(){
   	//Select Public for report
   	$myopt = '<option value="">---Select----</option>';
   	$myopt .= '<option value="Yes">Yes</option>';
   	$myopt .= '<option value="No">No</option>';
   	return $myopt;
   
   }
public function getOptonsHtmlTranslate($sql, $display, $value){
   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
   	$db = $this->getAdapter();
   	$option = '<option value="">--- Select ---</option>';
   	foreach($db->fetchAll($sql) as $r){
   		$option .= '<option value="'.$r[$value].'">'.htmlspecialchars($tr->translate(strtoupper($r[$display])), ENT_QUOTES).'</option>';
   	}
   	
   	return $option;
   }   
   public function getImgAttachStatus($rows,$base_url, $case=''){
		if($rows){			
			$imgattach='<img src="'.$base_url.'/images/icon/attachment.png"/>';
			$imgnone='<img src="'.$base_url.'/images/icon/no-attachment.png"/>';
			if($case !== ''){
				$imgattach='<img src="'.$base_url.'/images/icon/attachment.png"/>';
				$imgnone='<img src="'.$base_url.'/images/icon/no-attachment.png"/>';
			}
			 
			foreach ($rows as $i =>$row){
				if(is_dir('docs/case_note_id_'.$row['note_id'])){
					$rows[$i]['note_id'] = $imgattach;
				}
				else{
					$rows[$i]['note_id'] = $imgnone;
				}
			}
			 
		}		
		return $rows;
	}
	/**
	 * add element "delete" to $rows
	 * @param array $rows
	 * @param string $url_delete
	 * @param string $base_url
	 * @return array $rows
	 */
	public static function getImgDelete($rows,$url_delete,$base_url){
		foreach($rows as $key=>$row){
			$url = $url_delete.$row["id"];
			$row['delete'] = '<a href="'.$url.'"><img src="'.BASE_URL.'/images/icon/cross.png"/></a>';
			$rows[$key] = $row;
		}
		return $rows;
	}
	
	/**
	 * Get Day name With multiple Languages
	 * @param string $key
	 * @var $key ('mo', 'tu', 'we', 'th', 'fr', 'sa', 'su')
	 */
	public function getDayName($key = ''){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$day_name = array(
							'su' => $tr->translate('SU'),
							'mo' => $tr->translate('MO'),
							'tu' => $tr->translate('TU'),
							'we' => $tr->translate('WE'),
							'th' => $tr->translate('TH'),
							'fr' => $tr->translate('FR'),
							'sa' => $tr->translate('SA')							
						 );
		if(empty($key)){
			return $day_name;
		}
		return  $day_name[$key];
	}
	
	/**
	 * Get all Hour per day
	 * @param int $key
	 * @return multitype:string |Ambigous <string>
	 * @var $key = [0-23]
	 */
	public function getHours($key = ''){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$am = $tr->translate('AM');
		$pm = $tr->translate('PM');
		$hours = array(
				'12:00 '. $pm,
				'01:00 '. $am,
				'02:00 '. $am,
				'03:00 '. $am,
				'04:00 '. $am,
				'05:00 '. $am,
				'06:00 '. $am,
				'07:00 '. $am,
				'08:00 '. $am,
				'09:00 '. $am,
				'10:00 '. $am,
				'11:00 '. $am,
				'12:00 '. $am,
				'01:00 '. $pm,
				'02:00 '. $pm,
				'03:00 '. $pm,
				'04:00 '. $pm,
				'05:00 '. $pm,
				'06:00 '. $pm,
				'07:00 '. $pm,
				'08:00 '. $pm,
				'09:00 '. $pm,
				'10:00 '. $pm,
				'11:00 '. $pm				
				); 
		if(empty($key)){
			return $hours;
		}
		return  $hours[$key];
	}
	
	/**
	 * Generate Age for child
	 */

	public function getSelectBoxOptions($options){
		$sl_opt = "";
		foreach($options as $key=>$opt){
			$sl_opt .= "<option value='".$key."'>".$opt."</option>";
		}
		return $sl_opt;
	}	
	
	/**
	 * get phone number in format
	 * @param string $str
	 * @return string
	 */
	public static function getPhoneNumber($str)
	{
		$str = str_replace(" ", "", $str);
		$firt = substr($str, 0,3);
		$second = substr($str, 3, strlen($str)-3);
		$phone = $firt." ".$second;
		return $phone;
	}
	
	/**
	 * Generate navigation for use global
	 * @author channy
	 * @param $url current of action
	 * @param $frm form for use cover of control 
	 * @param $limit number of limit record
	 * @return $record_count number of record
	 */
// 		public function getList($url,$frm,$start,$limit,$record_count){
// 			$page = new Application_Form_FrmNavigation($url, $start, $limit, $record_count);
// 			$page->init($url, $start, $limit, $record_count);//can wrong $form
// 			$nevigation = $page->navigationPage();
// 			$rows_per_page = $page->getRowsPerPage($limit, $frm);
// 			$result_row = $page->getResultRows();
// 			$arr = array(
// 					"nevigation"=>$nevigation,
// 					"rows_per_page"=>$rows_per_page,
// 					"result_row"=>$result_row);
// 			return $arr;
// 		}
// 		public function getAllMetionOption(){
// 			$_db = new Application_Model_DbTable_DbGlobal();
// 			$rows = $_db->getAllMention();
// 			$option = '';
// 			if(!empty($rows))foreach($rows as $key => $value){
// 				$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
// 			}
// 			return $option;
// 		}
// 		public function getAllPayMentTermOption(){
// 			$_db = new Application_Model_DbTable_DbGlobal();
// 			$rows = $_db->getAllPaymentTerm();
// 			$option = '';
// 			if(!empty($rows))foreach($rows as $key => $value){
// 				$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
// 			}
// 			return $option;
// 		}
		public function getAllFacultyOption(){
			$_db = new Application_Model_DbTable_DbGlobal();
			$rows = $_db->getAllFecultyName();
			array_unshift($rows, array('dept_id'=>-1,'en_name'=>"Add New"));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['dept_id'].'" >'.htmlspecialchars($value['en_name'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		
		public function getImgActive($rows,$base_url, $case='',$degree=null,$display=null){
			if($rows){
				$imgnone='<img src="'.$base_url.'/images/icon/cross.png"/>';
				$imgtick='<img src="'.$base_url.'/images/icon/apply2.png"/>';
		
				foreach ($rows as $i =>$row){
					if($degree!=null){
						$dg = new Application_Model_DbTable_DbGlobal();
						
						$rows[$i]['degree']  = $dg->getAllDegree($row['degree']);
					}
					if($display!=null){
						$rows[$i]['displayby']= ($row['displayby']==1)?'Khmer':'English';
					}
					if (!empty($row['is_require'])){
						if($row['is_require'] == 1){
							$rows[$i]['is_require']= $imgtick;
						}
						else{
							$rows[$i]['is_require'] = $imgnone;
						
						}
					}
					if($row['status'] == 1){
						$rows[$i]['status']= $imgtick;
					}
					else{
						$rows[$i]['status'] = $imgnone;
						
					}
					
				}
			}
			return $rows;
		}
		public function getSex($rows,$base_url, $case='',$type=null){
			if($rows){
				$m='M';
				$f='F';
				foreach ($rows as $i =>$row){
					if($row['sex'] == 1){
						$rows[$i]['sex'] = $f;
					}
					else{
						$rows[$i]['sex'] = $m;
					}
				}
			}
			return $rows;
		}
		public function getAllClientGroupOption(){
			$_db = new Application_Model_DbTable_DbGlobal();
			$rows = $_db->getClientByType();
			array_unshift($rows, array('client_id'=>-1,'name_en'=>"Add New"));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['client_id'].'" >'.htmlspecialchars($value['name_en'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getAllClientCodeOption(){
			$_db = new Application_Model_DbTable_DbGlobal();
			$rows = $_db->getClientByType();
// 			array_unshift($rows, array('client_id'=>-1,'client_number'=>"Add New"));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['client_id'].'" >'.htmlspecialchars($value['client_number'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getCollecteralOption(){
			$db = new Application_Model_DbTable_DbGlobal();
			$rows= $db->getCollteralType();
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['title_en'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getCollecteralTypeOption(){
				$options= '<option value="1" >'.htmlspecialchars('ផ្ទាល់ខ្លួន', ENT_QUOTES).'</option>';
				$options .= '<option value="2" >'.htmlspecialchars('អ្នកធានាជំនួស', ENT_QUOTES).'</option>';
			return $options;
		}
		public function getTime(){
			$hours =array(''=>'',
					'00:00:00'=>'12:00 AM',
					'00:15:00'=>'12:15 AM',
					'00:30:00'=>'12:30 AM',
					'00:45:00'=>'12:45 AM',
					'01:00:00'=>'1:00 AM',
					'01:15:00'=>'1:15 AM',
					'01:30:00'=>'1:30 AM',
					'01:45:00'=>'1:45 AM',
					'02:00:00'=>'2:00 AM',
					'02:15:00'=>'2:15 AM',
					'02:30:00'=>'2:30 AM',
					'02:45:00'=>'2:45 AM',
					'03:00:00'=>'3:00 AM',
					'03:15:00'=>'3:15 AM',
					'03:30:00'=>'3:30 AM',
					'03:45:00'=>'3:45 AM',
					'04:00:00'=>'4:00 AM',
					'04:15:00'=>'4:15 AM',
					'04:30:00'=>'4:30 AM',
					'04:45:00'=>'4:45 AM',
					'05:00:00'=>'5:00 AM',
					'05:15:00'=>'5:15 AM',
					'05:30:00'=>'5:30 AM',
					'05:45:00'=>'5:45 AM',
					'06:00:00'=>'6:00 AM',
					'06:15:00'=>'6:15 AM',
					'06:30:00'=>'6:30 AM',
					'06:45:00'=>'6:45 AM',
					'07:00:00'=>'7:00 AM',
					'07:15:00'=>'7:15 AM',
					'07:30:00'=>'7:30 AM',
					'07:45:00'=>'7:45 AM',
					'08:00:00'=>'8:00 AM',
					'08:15:00'=>'8:15 AM',
					'08:30:00'=>'8:30 AM',
					'08:45:00'=>'8:45 AM',
					'09:00:00'=>'9:00 AM',
					'09:15:00'=>'9:15 AM',
					'09:30:00'=>'9:30 AM',
					'09:45:00'=>'9:45 AM',
					'10:00:00'=>'10:00 AM',
					'10:15:00'=>'10:15 AM',
					'10:30:00'=>'10:30 AM',
					'10:45:00'=>'10:45 AM',
					'11:00:00'=>'11:00 AM',
					'11:15:00'=>'11:15 AM',
					'11:30:00'=>'11:30 AM',
					'11:45:00'=>'11:45 AM',
		
					'12:00:00'=>'12:00 PM',
					'12:15:00'=>'12:15 PM',
					'12:30:00'=>'12:30 PM',
					'12:45:00'=>'12:45 PM',
					'13:00:00'=>'1:00 PM',
					'13:15:00'=>'1:15 PM',
					'13:30:00'=>'1:30 PM',
					'13:45:00'=>'1:45 PM',
					'14:00:00'=>'2:00 PM',
					'14:15:00'=>'2:15 PM',
					'14:30:00'=>'2:30 PM',
					'14:45:00'=>'2:45 PM',
					'15:00:00'=>'3:00 PM',
					'15:15:00'=>'3:15 PM',
					'15:30:00'=>'3:30 PM',
					'15:45:00'=>'3:45 PM',
					'16:00:00'=>'4:00 PM',
					'16:15:00'=>'4:15 PM',
					'16:30:00'=>'4:30 PM',
					'16:45:00'=>'4:45 PM',
					'17:00:00'=>'5:00 PM',
					'17:15:00'=>'5:15 PM',
					'17:30:00'=>'5:30 PM',
					'17:45:00'=>'5:45 PM',
					'18:00:00'=>'6:00 PM',
					'18:15:00'=>'6:15 PM',
					'18:30:00'=>'6:30 PM',
					'18:45:00'=>'6:45 PM',
					'19:00:00'=>'7:00 PM',
					'19:15:00'=>'7:15 PM',
					'19:30:00'=>'7:30 PM',
					'19:45:00'=>'7:45 PM',
					'20:00:00'=>'8:00 PM',
					'20:15:00'=>'8:15 PM',
					'20:30:00'=>'8:30 PM',
					'20:45:00'=>'8:45 PM',
					'21:00:00'=>'9:00 PM',
					'21:15:00'=>'9:15 PM',
					'21:30:00'=>'9:30 PM',
					'21:45:00'=>'9:45 PM',
					'22:00:00'=>'10:00 PM',
					'22:15:00'=>'10:15 PM',
					'22:30:00'=>'10:30 PM',
					'22:45:00'=>'10:45 PM',
					'23:00:00'=>'11:00 PM',
					'23:15:00'=>'11:15 PM',
					'23:30:00'=>'11:30 PM',
					'23:45:00'=>'11:45 PM',
			);
			return $hours;
		}
		
		public function getTimeView($row){
			$hours =array(''=>'',
					'00:00:00'=>'12:00 AM',
					'00:15:00'=>'12:15 AM',
					'00:30:00'=>'12:30 AM',
					'00:45:00'=>'12:45 AM',
					'01:00:00'=>'1:00 AM',
					'01:15:00'=>'1:15 AM',
					'01:30:00'=>'1:30 AM',
					'01:45:00'=>'1:45 AM',
					'02:00:00'=>'2:00 AM',
					'02:15:00'=>'2:15 AM',
					'02:30:00'=>'2:30 AM',
					'02:45:00'=>'2:45 AM',
					'03:00:00'=>'3:00 AM',
					'03:15:00'=>'3:15 AM',
					'03:30:00'=>'3:30 AM',
					'03:45:00'=>'3:45 AM',
					'04:00:00'=>'4:00 AM',
					'04:15:00'=>'4:15 AM',
					'04:30:00'=>'4:30 AM',
					'04:45:00'=>'4:45 AM',
					'05:00:00'=>'5:00 AM',
					'05:15:00'=>'5:15 AM',
					'05:30:00'=>'5:30 AM',
					'05:45:00'=>'5:45 AM',
					'06:00:00'=>'6:00 AM',
					'06:15:00'=>'6:15 AM',
					'06:30:00'=>'6:30 AM',
					'06:45:00'=>'6:45 AM',
					'07:00:00'=>'7:00 AM',
					'07:15:00'=>'7:15 AM',
					'07:30:00'=>'7:30 AM',
					'07:45:00'=>'7:45 AM',
					'08:00:00'=>'8:00 AM',
					'08:15:00'=>'8:15 AM',
					'08:30:00'=>'8:30 AM',
					'08:45:00'=>'8:45 AM',
					'09:00:00'=>'9:00 AM',
					'09:15:00'=>'9:15 AM',
					'09:30:00'=>'9:30 AM',
					'09:45:00'=>'9:45 AM',
					'10:00:00'=>'10:00 AM',
					'10:15:00'=>'10:15 AM',
					'10:30:00'=>'10:30 AM',
					'10:45:00'=>'10:45 AM',
					'11:00:00'=>'11:00 AM',
					'11:15:00'=>'11:15 AM',
					'11:30:00'=>'11:30 AM',
					'11:45:00'=>'11:45 AM',
		
					'12:00:00'=>'12:00 PM',
					'12:15:00'=>'12:15 PM',
					'12:30:00'=>'12:30 PM',
					'12:45:00'=>'12:45 PM',
					'13:00:00'=>'1:00 PM',
					'13:15:00'=>'1:15 PM',
					'13:30:00'=>'1:30 PM',
					'13:45:00'=>'1:45 PM',
					'14:00:00'=>'2:00 PM',
					'14:15:00'=>'2:15 PM',
					'14:30:00'=>'2:30 PM',
					'14:45:00'=>'2:45 PM',
					'15:00:00'=>'3:00 PM',
					'15:15:00'=>'3:15 PM',
					'15:30:00'=>'3:30 PM',
					'15:45:00'=>'3:45 PM',
					'16:00:00'=>'4:00 PM',
					'16:15:00'=>'4:15 PM',
					'16:30:00'=>'4:30 PM',
					'16:45:00'=>'4:45 PM',
					'17:00:00'=>'5:00 PM',
					'17:15:00'=>'5:15 PM',
					'17:30:00'=>'5:30 PM',
					'17:45:00'=>'5:45 PM',
					'18:00:00'=>'6:00 PM',
					'18:15:00'=>'6:15 PM',
					'18:30:00'=>'6:30 PM',
					'18:45:00'=>'6:45 PM',
					'19:00:00'=>'7:00 PM',
					'19:15:00'=>'7:15 PM',
					'19:30:00'=>'7:30 PM',
					'19:45:00'=>'7:45 PM',
					'20:00:00'=>'8:00 PM',
					'20:15:00'=>'8:15 PM',
					'20:30:00'=>'8:30 PM',
					'20:45:00'=>'8:45 PM',
					'21:00:00'=>'9:00 PM',
					'21:15:00'=>'9:15 PM',
					'21:30:00'=>'9:30 PM',
					'21:45:00'=>'9:45 PM',
					'22:00:00'=>'10:00 PM',
					'22:15:00'=>'10:15 PM',
					'22:30:00'=>'10:30 PM',
					'22:45:00'=>'10:45 PM',
					'23:00:00'=>'11:00 PM',
					'23:15:00'=>'11:15 PM',
					'23:30:00'=>'11:30 PM',
					'23:45:00'=>'11:45 PM',
			);
			return $hours[$row['delivey_time']]=$row;
		}
		
		public function getHoursStudy($rows){
			$hours =array('00:00:00'=>'12:00 AM',
					'00:15:00'=>'12:15 AM',
					'00:30:00'=>'12:30 AM',
					'00:45:00'=>'12:45 AM',
					'01:00:00'=>'1:00 AM',
					'01:15:00'=>'1:15 AM',
					'01:30:00'=>'1:30 AM',
					'01:45:00'=>'1:45 AM',
					'02:00:00'=>'2:00 AM',
					'02:15:00'=>'2:15 AM',
					'02:30:00'=>'2:30 AM',
					'02:45:00'=>'2:45 AM',
					'03:00:00'=>'3:00 AM',
					'03:15:00'=>'3:15 AM',
					'03:30:00'=>'3:30 AM',
					'03:45:00'=>'3:45 AM',
					'04:00:00'=>'4:00 AM',
					'04:15:00'=>'4:15 AM',
					'04:30:00'=>'4:30 AM',
					'04:45:00'=>'4:45 AM',
					'05:00:00'=>'5:00 AM',
					'05:15:00'=>'5:15 AM',
					'05:30:00'=>'5:30 AM',
					'05:45:00'=>'5:45 AM',
					'06:00:00'=>'6:00 AM',
					'06:15:00'=>'6:15 AM',
					'06:30:00'=>'6:30 AM',
					'06:45:00'=>'6:45 AM',
					'07:00:00'=>'7:00 AM',
					'07:15:00'=>'7:15 AM',
					'07:30:00'=>'7:30 AM',
					'07:45:00'=>'7:45 AM',
					'08:00:00'=>'8:00 AM',
					'08:15:00'=>'8:15 AM',
					'08:30:00'=>'8:30 AM',
					'08:45:00'=>'8:45 AM',
					'09:00:00'=>'9:00 AM',
					'09:15:00'=>'9:15 AM',
					'09:30:00'=>'9:30 AM',
					'09:45:00'=>'9:45 AM',
					'10:00:00'=>'10:00 AM',
					'10:15:00'=>'10:15 AM',
					'10:30:00'=>'10:30 AM',
					'10:45:00'=>'10:45 AM',
					'11:00:00'=>'11:00 AM',
					'11:15:00'=>'11:15 AM',
					'11:30:00'=>'11:30 AM',
					'11:45:00'=>'11:45 AM',
						
					'12:00:00'=>'12:00 PM',
					'12:15:00'=>'12:15 PM',
					'12:30:00'=>'12:30 PM',
					'12:45:00'=>'12:45 PM',
					'13:00:00'=>'1:00 PM',
					'13:15:00'=>'1:15 PM',
					'13:30:00'=>'1:30 PM',
					'13:45:00'=>'1:45 PM',
					'14:00:00'=>'2:00 PM',
					'14:15:00'=>'2:15 PM',
					'14:30:00'=>'2:30 PM',
					'14:45:00'=>'2:45 PM',
					'15:00:00'=>'3:00 PM',
					'15:15:00'=>'3:15 PM',
					'15:30:00'=>'3:30 PM',
					'15:45:00'=>'3:45 PM',
					'16:00:00'=>'4:00 PM',
					'16:15:00'=>'4:15 PM',
					'16:30:00'=>'4:30 PM',
					'16:45:00'=>'4:45 PM',
					'17:00:00'=>'5:00 PM',
					'17:15:00'=>'5:15 PM',
					'17:30:00'=>'5:30 PM',
					'17:45:00'=>'5:45 PM',
					'18:00:00'=>'6:00 PM',
					'18:15:00'=>'6:15 PM',
					'18:30:00'=>'6:30 PM',
					'18:45:00'=>'6:45 PM',
					'19:00:00'=>'7:00 PM',
					'19:15:00'=>'7:15 PM',
					'19:30:00'=>'7:30 PM',
					'19:45:00'=>'7:45 PM',
					'20:00:00'=>'8:00 PM',
					'20:15:00'=>'8:15 PM',
					'20:30:00'=>'8:30 PM',
					'20:45:00'=>'8:45 PM',
					'21:00:00'=>'9:00 PM',
					'21:15:00'=>'9:15 PM',
					'21:30:00'=>'9:30 PM',
					'21:45:00'=>'9:45 PM',
					'22:00:00'=>'10:00 PM',
					'22:15:00'=>'10:15 PM',
					'22:30:00'=>'10:30 PM',
					'22:45:00'=>'10:45 PM',
					'23:00:00'=>'11:00 PM',
					'23:15:00'=>'11:15 PM',
					'23:30:00'=>'11:30 PM',
					'23:45:00'=>'11:45 PM',
			);
			foreach ($rows as $i =>$row){
				if($row){
					$rows[$i]['delivey_time']= $hours[$row['delivey_time']];
				} 
			}
			return $rows;
		}
		
		public function getHoursAlert($rows){
			$hours =array('00:00:00'=>'12:00:00',
					'00:15:00'=>'12:15:00',
					'00:30:00'=>'12:30:00',
					'00:45:00'=>'12:45:00',
					'01:00:00'=>'1:00:00',
					'01:15:00'=>'1:15:00',
					'01:30:00'=>'1:30:00',
					'01:45:00'=>'1:45:00',
					'02:00:00'=>'2:00:00',
					'02:15:00'=>'2:15:00',
					'02:30:00'=>'2:30:00',
					'02:45:00'=>'2:45:00',
					'03:00:00'=>'3:00:00',
					'03:15:00'=>'3:15:00',
					'03:30:00'=>'3:30:00',
					'03:45:00'=>'3:45:00',
					'04:00:00'=>'04:00:00',
					'04:15:00'=>'4:15:00',
					'04:30:00'=>'4:30:00',
					'04:45:00'=>'4:45:00',
					'05:00:00'=>'5:00:00',
					'05:15:00'=>'5:15:00',
					'05:30:00'=>'5:30:00',
					'05:45:00'=>'5:45:00',
					'06:00:00'=>'6:00:00',
					'06:15:00'=>'6:15:00',
					'06:30:00'=>'6:30:00',
					'06:45:00'=>'6:45:00',
					'07:00:00'=>'7:00:00',
					'07:15:00'=>'7:15:00',
					'07:30:00'=>'7:30:00',
					'07:45:00'=>'7:45:00',
					'08:00:00'=>'8:00:00',
					'08:15:00'=>'8:15:00',
					'08:30:00'=>'8:30:00',
					'08:45:00'=>'8:45:00',
					'09:00:00'=>'9:00:00',
					'09:15:00'=>'9:15:00',
					'09:30:00'=>'9:30:00',
					'09:45:00'=>'9:45:00',
					'10:00:00'=>'10:00:00',
					'10:15:00'=>'10:15:00',
					'10:30:00'=>'10:30:00',
					'10:45:00'=>'10:45:00',
					'11:00:00'=>'11:00:00',
					'11:15:00'=>'11:15:00',
					'11:30:00'=>'11:30:00',
					'11:45:00'=>'11:45:00',
		
					'12:00:00'=>'12:00:00',
					'12:15:00'=>'12:15:00',
					'12:30:00'=>'12:30:00',
					'12:45:00'=>'12:45:00',
					'13:00:00'=>'01:00:00',
					'13:15:00'=>'01:15:00',
					'13:30:00'=>'01:30:00',
					'13:45:00'=>'01:45:00',
					'14:00:00'=>'02:00:00',
					'14:15:00'=>'02:15:00',
					'14:30:00'=>'02:30:00',
					'14:45:00'=>'02:45:00',
					'15:00:00'=>'03:00:00',
					'15:15:00'=>'03:15:00',
					'15:30:00'=>'03:30:00',
					'15:45:00'=>'03:45:00',
					'16:00:00'=>'04:00:00',
					'16:15:00'=>'04:15:00',
					'16:30:00'=>'04:30:00',
					'16:45:00'=>'04:45:00',
					'17:00:00'=>'05:00:00',
					'17:15:00'=>'05:15:00',
					'17:30:00'=>'05:30:00',
					'17:45:00'=>'05:45:00',
					'18:00:00'=>'06:00:00',
					'18:15:00'=>'06:15:00',
					'18:30:00'=>'06:30:00',
					'18:45:00'=>'06:45:00',
					'19:00:00'=>'07:00:00',
					'19:15:00'=>'07:15:00',
					'19:30:00'=>'07:30:00',
					'19:45:00'=>'07:45:00',
					'20:00:00'=>'08:00:00',
					'20:15:00'=>'08:15:00',
					'20:30:00'=>'08:30:00',
					'20:45:00'=>'08:45:00',
					'21:00:00'=>'09:00:00',
					'21:15:00'=>'09:15:00',
					'21:30:00'=>'09:30:00',
					'21:45:00'=>'09:45:00',
					'22:00:00'=>'10:00:00',
					'22:15:00'=>'10:15:00',
					'22:30:00'=>'10:30:00',
					'22:45:00'=>'10:45:00',
					'23:00:00'=>'11:00:00',
					'23:15:00'=>'11:15:00',
					'23:30:00'=>'11:30:00',
					'23:45:00'=>'11:45:00 ',
			);
		
			foreach ($rows as $i =>$row){
				if($row){
					$rows[$i]['time_zone']= $hours[$row['time_zone']];
				}
			}
			return $rows;
		
		}
		
}

