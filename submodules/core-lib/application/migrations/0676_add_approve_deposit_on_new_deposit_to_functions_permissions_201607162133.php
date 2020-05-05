<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_approve_deposit_on_new_deposit_to_functions_permissions_201607162133 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('set_settled_on_new_deposit', 'Set settled on new deposit', 160, 72, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(160);
	}

}