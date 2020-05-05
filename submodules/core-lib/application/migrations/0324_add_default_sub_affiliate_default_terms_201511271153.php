<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_default_sub_affiliate_default_terms_201511271153 extends CI_Migration {

	public function up() {
		// $this->db->where('name', 'sub_affiliate_default_terms');
		// $this->db->update('operator_settings', array('value' => '{"terms": {"terms_type": "allow","sub_allowed":"manual","manual_open":"manual","sub_link":"link","level_master":"","sub_level":"30","sub_levels":",,,,,,,,,,,,,,,,,,,,,,,,,,,,,"}}'));
	}

	public function down() {
		// $this->db->where('name', 'sub_affiliate_default_terms');
		// $this->db->update('operator_settings', array('value' => ''));
	}
}