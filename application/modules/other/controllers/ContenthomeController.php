<?php
class Other_contenthomeController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db = new Other_Model_DbTable_DbSlide();
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$data['home_article']="home_article";
				
					$id= $db->homecontent($data,"home_article");
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}		
		$this->view->home_article = $db->getWebsiteSetting("home_article");
		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
		$this->view->lang = $dbglobal->getLaguage();
	}
}


