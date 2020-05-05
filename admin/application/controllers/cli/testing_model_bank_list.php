<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_bank_list extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('bank_list');
		$this->lang->load('main', 'chinese');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->testGetBankTypeTree();
	}

	//it's your real test function
	private function testGetBankTypeTree() {
		$systemId = IPS_PAYMENT_API;

		$this->utils->debug_log($this->bank_list->getBankTypeTree($systemId));
	}

}

///end of file/////////////