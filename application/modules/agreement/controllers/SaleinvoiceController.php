<?php
class Agreement_saleinvoiceController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	const REDIRECT_URL='/agreement';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db =new agreement_Model_DbTable_Carsale();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'title' => $formdata['title'],
						'status_search'=>$formdata['status_search'],
				);
			}
			else{
				$search = array(
						'title' => '',
						'status_search' => -1,
				);
			}
	
			$rs_rows= $db->getAllCarSaleAgreement($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Agreement Code","Year","Make","Model","Sub Model","Plaque No","Owner Name","Contac","Buyer","Contact","Price in US$","Balance");
			$link=array(
					'module'=>'agreement','controller'=>'carsale','action'=>'invoice',
			);
	
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('id'=>$link,'o_name'=>$link,'name'=>$link,'ag_code'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		// 		$frm = new Location_Form_FrmSearch();
		// 		$frm =$frm->search();
		// 		Application_Model_Decorator::removeAllDecorator($frm);
		// 		$this->view->frm_search = $frm;
	}
}

