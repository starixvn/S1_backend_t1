<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_20151001 extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `operator_settings` (`id`, `name`, `value`, `note`)
		// 			VALUES
		// 				(15, 'ip_rules', 'false', 'true = use ip list, false = ignore ip list')
		// 			");
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE id=15');
	}
}