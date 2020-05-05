<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_20161108 extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `operator_settings` (`id`, `name`, `value`, `note`)
		// 			VALUES
		// 				(101, 'cronjob_sync_http_request', 'false', 'Sync http request record to http reques summary table')
		// 			");
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE id=101');
	}
}