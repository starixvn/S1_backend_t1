<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adjust_manually_wallet_permission_to_functions_201607172245 extends CI_Migration {

	// const FUNC_ID = 161;  # Current largest + 1
	// const PARENT_ID = 72; # Payment Management

	# This function defines whether one can manage the orders with wait_API status

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('adjust_manually_wallet', 'Adjust manually wallet', self::FUNC_ID, self::PARENT_ID, true);

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