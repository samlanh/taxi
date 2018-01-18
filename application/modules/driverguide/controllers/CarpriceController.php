<?php
class Driverguide_CarpriceController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_carprice=new Driverguide_Model_DbTable_DbCarPricePickUp();
		$rows=$db_carprice->getAllCarprice();
		$glClass = new Application_Model_GlobalClass();
		$rows = $glClass->getImgActive($rows, BASE_URL, true);
		try{
			$list = new Application_Form_Frmtable();
			$collumns = array("Vehicle Ref No","Frame No","Tax","From Location","To Location","Price","Note","Status");
			$link=array(
					'module'=>'driverguide','controller'=>'carprice','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('vehicle_id'=>$link,'frame_no'=>$link,'tax'=>$link,'form_location'=>$link,'to_location'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function addAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
            $db=new Driverguide_Model_DbTable_DbCarPricePickUp();
			try{
				if(isset($data['save_new'])){
				    $db->addCarprice($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				elseif(!empty($data['save_close'])){
					$db->addCarprice($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/carprice");
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
	function editAction(){
		$db_model = new Driverguide_Model_DbTable_Dbvehicleprice();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
            $db=new Driverguide_Model_DbTable_DbCarPricePickUp();
			try{
				if(!empty($data['save_close'])){
					$db->updateCarprice($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/carprice");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id=$this->getRequest()->getParam('id');
		$db_carprice=new Driverguide_Model_DbTable_DbCarPricePickUp();
		$this->view->row_carprice=$db_carprice->getCarpriceById($id);
		$db = new Application_Model_GlobalClass();
		$this->view->pro_option = $db->getAllLocationOption();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs_tax =$db->getAllTax();
		$this->view->vehicle=$db_model->getVehecleName();
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
}

