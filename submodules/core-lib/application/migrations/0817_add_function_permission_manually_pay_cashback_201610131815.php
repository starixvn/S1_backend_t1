<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_permission_manually_pay_cashback_201610131815 extends CI_Migration {
	const PARENT_ID = 72;
	const FUNC_ID = 170;
	const FUNC_NAME = 'Manually Pay Cashback';
	const FUNC_CODE = 'manually_pay_cashback';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction(self::FUNC_CODE, self::FUNC_NAME, self::FUNC_ID, self::PARENT_ID, true);

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

////END OF FILE////