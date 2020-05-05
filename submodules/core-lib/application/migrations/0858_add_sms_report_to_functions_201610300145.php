<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sms_report_to_functions_201610300145 extends CI_Migration {

	// const FUNC_ID = 182;
	// const PARENT_ID = 40;
	// const FUNC_CODE = 'sms_report';
	// const FUNC_NAME = 'SMS Verification Codes';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction(self::FUNC_CODE, self::FUNC_NAME, self::FUNC_ID, self::PARENT_ID);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(self::FUNC_ID);
	}

}
