<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Delete_row_affiliate_main_rule_to_operator_settings_201511071102 extends CI_Migration {

	public function up() {
		// $this->db->query('DELETE FROM operator_settings WHERE name = "affiliate_main_percentage" OR name = "affiliate_main_active"');
	}

	public function down() {
// 		$query = <<<EOD
// INSERT
// 	INTO `operator_settings`
// 	(`name`, `value`, `note`)
// 	VALUES
// 	('affiliate_main_percentage', '0', 'main affiliate percentage rule'),
// 	('affiliate_main_active', '0', 'main affiliate active players rule')
// EOD;
// 		$this->db->query($query);
	}
}