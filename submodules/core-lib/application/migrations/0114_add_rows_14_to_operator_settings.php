<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_14_to_operator_settings extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `operator_settings` (`id`, `name`, `value`, `note`)
		// VALUES
		// 	(14, 'promo_cancellation_setting', 1,'0-manual, 1-auto')
		// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `operator_settings');
	}
}