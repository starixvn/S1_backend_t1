<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_for_agency_to_functions_201606010422 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('view_agent', 'View Agent', 131, 116);
		// $this->roles->initFunction('edit_agency_settings', 'Edit Agency Settings', 132, 116);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->roles->deleteFunction(131);
		// $this->roles->deleteFunction(132);
	}

}
