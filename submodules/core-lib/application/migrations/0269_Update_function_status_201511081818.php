<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_function_status_201511081818 extends CI_Migration {

	public function up() {
		// $this->db->query("UPDATE functions SET status = 1 WHERE funcId IN (28, 29, 30)");
	}

	public function down() {
		// $this->db->query("UPDATE functions SET status = 2 WHERE funcId IN (28, 29, 30)");
	}
}