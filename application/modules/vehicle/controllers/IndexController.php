<?php
class Vehicle_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	const REDIRECT_URL_ADD ='/vehicle/index/add';
	const REDIRECT_URL_ADD_CLOSE ='/vehicle/index/';
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
		$db_make = new Vehicle_Model_DbTable_DbVehicle();
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search' => '',
					'make'=> -1,
					'model'=> -1,
					'submodel'=> -1,
					'search_status' =>-1,
					'vehicle_type'=>-1,
					'year'=>-1
					);
		}
		$rows=$db_make->getAllVehicle($search);
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("Vehicle Ref","Year","Make","Model","Sub Model","Type","Color","Engine No","Chassis No","Frame No","Plate No","Date","STATUS");
			$link=array(
					'module'=>'vehicle','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('reffer'=>$link,'year'=>$link,'make_id'=>$link,'model_id'=>$link,'sub_model'=>$link,'status'=>$link,'car_type'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$rows_veh_typ=$db_make->getAllVehicleType();
		$this->view->rows_veh_typ=$rows_veh_typ;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		//array_unshift($model, array ( 'id' => -1, 'name' => 'Selected Make') );
		$this->view->all_make=$model;
		
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			try {
				$db_make = new Vehicle_Model_DbTable_DbVehicle();
				if(isset($data['save_new'])){
					$db_make->addVehicle($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD);
				}else if(isset($data['save_close'])){
					$db_make->addVehicle($data);
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
				}
			}catch (Exception $e) {
				print_r($e->getMessage());exit();
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_engin=$db->getAllEnGince();
		$this->view->rows_engine=$rows_engin;
		
		$rows_enginAsName=$db->getAllEnGinceAsname();
		array_unshift($rows_enginAsName, array ( 'id' => 0, 'name' => $tr->translate("Choose Horse Power")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_enginename=$rows_enginAsName;
		
		$rows_type=$db->getAllType();
		$this->view->rows_type=$rows_type;
		$rows_typename=$db->getAllTypeAsName();
		array_unshift($rows_typename, array ( 'id' => 0, 'name' => $tr->translate("Choose Type")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_typename=$rows_typename;
		
		$rows_tran=$db->getAllTransmisstion();
		$this->view->rows_tran=$rows_tran;
		
		$rows_veh_typ=$db->getAllVehicleType();
		$this->view->rows_veh_typ=$rows_veh_typ;
		$rows_veh_typAsname=$db->getAllVehicleTypeAsName();
		array_unshift($rows_veh_typAsname, array ( 'id' => 0, 'name' => $tr->translate("Choose Vehicle Type")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_veh_typasname=$rows_veh_typAsname;
		//select store mark
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' =>$tr->translate("ADD_NEW")) );
		$this->view->all_make=$model;
		$this->view->rs_tax =$db->getAllTax();
		
// 		$dbGC = new Application_Model_GlobalClass();
// 		$this->view->pro_option = $dbGC->getAllPackageDayOption();
// 		$this->view->location_option = $dbGC->getAllLocationOption();
	}
	function editAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			try{
				$db_make = new Vehicle_Model_DbTable_DbVehicle();
				if(isset($data['save_close'])){
					$db_make->updateVehicle($data);
					$this->_redirect(self::REDIRECT_URL_ADD_CLOSE);
					//Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL_ADD_CLOSE);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$id=$this->getRequest()->getParam('id');
		$db=new Vehicle_Model_DbTable_DbVehicle();
		$rows_v=$db->getVehicleById($id);
		$this->view->row_vehicle=$rows_v;
		$rows_engin=$db->getAllEnGince();
		$this->view->rows_engine=$rows_engin;
		
// 		$this->view->rows = $db->getVehiclePriceById($id);//get car rental price by package
// 		$this->view->row_carprice=$db->getCarpriceById($id);// get Price Rental Vehicle By Location to Location
		
		$rows_enginAsName=$db->getAllEnGinceAsname();
		array_unshift($rows_enginAsName, array ( 'id' => 0, 'name' => $tr->translate("Choose Horse Power")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_enginename=$rows_enginAsName;
		
		$rows_type=$db->getAllType();
		$this->view->rows_type=$rows_type;
		$rows_typename=$db->getAllTypeAsName();
		array_unshift($rows_typename, array ( 'id' => 0, 'name' => $tr->translate("Choose Type")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_typename=$rows_typename;
		
		$rows_tran=$db->getAllTransmisstion();
		$this->view->rows_tran=$rows_tran;
		
		$rows_veh_typ=$db->getAllVehicleType();
		$this->view->rows_veh_typ=$rows_veh_typ;
		$rows_veh_typAsname=$db->getAllVehicleTypeAsName();
		array_unshift($rows_veh_typAsname, array ( 'id' => 0, 'name' => $tr->translate("Choose Vehicle Type")), array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->rows_veh_typasname=$rows_veh_typAsname;
		
		//select store mark
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		array_unshift($model, array ( 'id' => -1, 'name' => $tr->translate("ADD_NEW")) );
		$this->view->all_make=$model;
		$this->view->rs_tax =$db->getAllTax();
		
// 		$dbGC = new Application_Model_GlobalClass();
// 		$this->view->pro_option = $dbGC->getAllPackageDayOption();
// 		$this->view->location_option = $dbGC->getAllLocationOption();
		
	}
	function getSubModelAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Vehicle_Model_DbTable_DbVehicle();
			$makes = $db->getAllSubModelById($data['model_id']);
			array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
			print_r(Zend_Json::encode($makes));
			exit();
		}
	}
	function getSubModelsearchAction(){//for search on vehicle index
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Vehicle_Model_DbTable_DbVehicle();
			$makes = $db->getAllSubModelById($data['model_id']);
			print_r(Zend_Json::encode($makes));
			exit();
		}
	}
	function addSubModelAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Vehicle_Model_DbTable_DbVehicle();
			$id = $_dbmodel->addSubModelajx($_data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
}

