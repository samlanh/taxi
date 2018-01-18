<?php
class Driverguide_VehiclepriceController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_make = new Driverguide_Model_DbTable_Dbvehicleprice();
		try{
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
						'type' =>-1,
				);
			}
			$rows=$db_make->getAllVehiclePrice($search);
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgActive($rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("Vehicle Ref","Frame No","Licence No","YEAR","MAKE","MODEL","Sub Model","TYPE","Tax","DATE","STATUS");
			$link=array('module'=>'driverguide','controller'=>'vehicleprice','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('reffer'=>$link,'licence_plate'=>$link,'frame_no'=>$link,'year'=>$link,'sub_model'=>$link,'model_id'=>$link,'type'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$rows_type=$db_make->getAllType();
		$this->view->rows_type=$rows_type;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$model = $db->getAllMake();
		$this->view->all_make=$model;
		
		$status=$db->getViews(2);
		$this->view->status_view=$status;
		
		
	}
	public function addAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db_model->addVehicleRental($data);
				if(isset($data['save_new'])){
					$this->_redirect("/driverguide/vehicleprice/add");
// 					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				else{
					$this->_redirect("/driverguide/vehicleprice");
// 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/vehicleprice");
				}
		
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllPackageDayOption();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$this->view->vehicle=$db_model->getVehecleName();
		
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
	public function editAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db_model->updateVehicleRental($data);
				$this->_redirect("/driverguide/vehicleprice");
// 			    Application_Form_FrmMessage::Sucessfull("Edit Success","/driverguide/vehicleprice");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->package_option = $db->getAllPackageDayOption();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$this->view->vehicle=$db_model->getVehecleName();
		
		$id = $this->getRequest()->getParam("id");
		$this->view->rows = $db_model->getVehiclePriceById($id);
// 		print_r($db_model->getVehiclePriceById($id));
		$this->view->vehicle_id=$id;
// 		$db=new Vehicle_Model_DbTable_DbVehicle();
// 		$this->view->vehicle=$db->getVehicleById($id);
	}
	function getVehicleAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Driverguide_Model_DbTable_Dbvehicleprice();
			$rs = $db->getVehicleById($data['vehicle_id']);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	
	
}

