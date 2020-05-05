<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_rolefunctions_giving extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `rolefunctions_giving` (`id`, `roleId`, `funcId`)
		// VALUES
		// 	(559, 1, 78),
		// 	(560, 1, 79),
		// 	(561, 3, 78),
		// 	(562, 3, 79),
		// 	(563, 10, 78),
		// 	(564, 10, 79),
		// 	(565, 1, 80)
		// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `rolefunctions_giving');
	}
}