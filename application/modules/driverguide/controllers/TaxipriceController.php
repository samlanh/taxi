<?php
class Driverguide_TaxipriceController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_taxi = new Driverguide_Model_DbTable_DbTaxiprice();
		$rows=$db_taxi->getAllVehicleTaxi();
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Vehicle Ref","From Location","To Location","Distance","Rate","One Way","Discount","Round Trip","Tax","Status");
			$link=array(
					'module'=>'driverguide','controller'=>'taxiprice','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('vehicle_id'=>$link,'from_location'=>$link,'to_location'=>$link,'distance'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function addAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
            $db=new Driverguide_Model_DbTable_DbTaxiprice();
			try{
				if(isset($data['save_new'])){
				    $db->addVehicletaxi($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				elseif(!empty($data['save_close'])){
					$db->addVehicletaxi($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/taxiprice");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllLocationOption();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$this->view->vehicle=$db_model->getVehecleName();
		
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
	public function editAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db=new Driverguide_Model_DbTable_DbTaxiprice();
			try{
				if(!empty($data['save_close'])){
					$db->updateVehicletaxi($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/taxiprice");
				}
			}catch (Exception $e){
				//echo $e->getMessage();exit();
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllLocationOption();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$this->view->vehicle=$db_model->getVehecleName();
		$status=$db->getViews(2);
		$this->view->status_view=$status;
		
		$db=new Driverguide_Model_DbTable_DbTaxiprice();
		$row=$this->view->row_taxi=$db->getVehicleTaxiById($id);
		
	}
}

