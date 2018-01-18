<?php
class Driverguide_ServicepriceController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_taxi = new Driverguide_Model_DbTable_DbServicePrice();
		$this->view->row_service=$db_taxi->getAllServicePrice();
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
            $db=new Driverguide_Model_DbTable_DbServicePrice();
			try{
				if(isset($data['save_new'])){
				    $db->addServicePrice($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				elseif(!empty($data['save_close'])){
					$db->addServicePrice($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/serviceprice");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$status=$db->getViews(2);
		$this->view->status_view=$status;
	}
	public function editAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db=new Driverguide_Model_DbTable_DbServicePrice();
			try{
				if(!empty($data['save_close'])){
					$db->updateServicePrice($data);
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESSS","/driverguide/serviceprice");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
        $id=$this->getRequest()->getParam('id');
        $db=new Driverguide_Model_DbTable_DbServicePrice();
        $this->view->row_service=$db->getServiceById($id);
        
        $db = new Application_Model_DbTable_DbGlobal();
        $status=$db->getViews(2);
        $this->view->status_view=$status;
	}
}

