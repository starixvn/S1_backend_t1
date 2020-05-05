<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_rolefunctions extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `rolefunctions` (`id`, `roleId`, `funcId`)
		// VALUES
		// 	(659, 1, 78),
		// 	(660, 1, 79),
		// 	(661, 1, 80),
		// 	(662, 1, 81)
		// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `rolefunctions');
	}
}