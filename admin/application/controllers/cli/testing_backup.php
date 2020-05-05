<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_backup extends BaseTesting {

	
	public function init() {
		$this->load->model('duplicate_account_setting');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		echo "pasok!";

	}

	public function backup_tables(){
		
	}

}
?>