<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_row_affiliate_default_terms_to_operator_settings_201511071117 extends CI_Migration {

	public function up() {
// 		$query = <<<EOD
// INSERT
// 	INTO `operator_settings`
// 	(`name`, `value`, `note`)
// 	VALUES
// 	('affiliate_default_terms', '0', 'affiliate default terms in json format'),
// 	('sub_affiliate_default_terms', '0', 'sub affiliate default terms in json format')
// EOD;
// 		$this->db->query($query);
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE name = "affiliate_default_terms" OR name = "sub_affiliate_default_terms"');
	}
}