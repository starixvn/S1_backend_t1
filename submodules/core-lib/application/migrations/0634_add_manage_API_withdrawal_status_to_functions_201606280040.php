<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_manage_API_withdrawal_status_to_functions_201606280040 extends CI_Migration {

	// const FUNC_ID = 146;  # Current largest + 1
	// const PARENT_ID = 72; # Payment Management
	// const FUNC_CODE = 'manage_API_withdrawal_status';
	// const FUNC_NAME = 'Manage API withdrawal status';

	# This function defines whether one can manage the orders with wait_API status

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