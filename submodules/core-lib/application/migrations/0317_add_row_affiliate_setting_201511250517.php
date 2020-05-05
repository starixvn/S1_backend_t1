<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_affiliate_setting_201511250517 extends CI_Migration {

	public function up() {
// 		$query = <<<EOD
// INSERT
// 	INTO `operator_settings`
// 	(`name`, `value`, `note`)
// 	VALUES
// 	('affiliate_settings', '0', 'affiliate set by operator in json format')
// EOD;
// 		$this->db->query($query);
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE name = "affiliate_settings"');
	}
}