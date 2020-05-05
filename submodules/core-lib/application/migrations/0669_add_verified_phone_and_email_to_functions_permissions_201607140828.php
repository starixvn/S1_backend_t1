<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_verified_phone_and_email_to_functions_permissions_201607140828 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('verified_phone_and_email_info', 'Verified Phone and Email Info', 158, 15, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(158);
	}

}