<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_admin_support_live_chat_to_functions_permissions_201607182355 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('admin_support_live_chat', 'Admin Support Live Chat', 162, 1, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(162);
	}

}