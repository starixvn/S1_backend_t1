<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_external_system extends CI_Migration {

	public function up() {
// 		$this->db->query("INSERT INTO `external_system` (`id`, `system_name`, `note`)
// VALUES
// 	(1, 'PT_API', ''),
// 	(2, 'AG_API', ''),
// 	(3, 'AG_FTP', '');
// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `external_system');
	}
}