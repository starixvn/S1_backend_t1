<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_api_docs_to_functions_permissions_201607142358 extends CI_Migration {

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('system_api_docs', 'System API Documents', 159, 1, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(159);
	}

}