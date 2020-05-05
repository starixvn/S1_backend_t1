<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_affiliatemodel extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('affiliatemodel');
		$this->lang->load('main', 'chinese');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testGetAllActivtedAffiliates() {
		$rows = $this->affiliatemodel->getAllActivtedAffiliates();

	}

}

///end of file/////////////