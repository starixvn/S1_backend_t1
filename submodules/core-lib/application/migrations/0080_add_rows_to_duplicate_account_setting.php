<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_duplicate_account_setting extends CI_Migration {

	public function up() {
		$this->db->query("INSERT INTO `duplicate_account_setting` (`id`, `item_id`, `rate_exact`, `rate_similar`, `status`)
VALUES
	(1, 1, 0, 0, 1),
	(2, 2, 0, 0, 1),
	(3, 3, 0, 0, 1),
	(4, 4, 0, 0, 1),
	(5, 5, 0, 0, 1),
	(6, 6, 0, 0, 1),
	(7, 7, 0, 0, 1),
	(8, 8, 0, 0, 1),
	(9, 9, 0, 0, 1),
	(10, 10, 0, 0, 1),
	(11, 11, 0, 0, 1),
	(12, 12, 0, 0, 1),
	(13, 13, 0, 0, 1);
");
	}

	public function down() {
		$this->db->query('DELETE * FROM `duplicate_account_setting');
	}
}