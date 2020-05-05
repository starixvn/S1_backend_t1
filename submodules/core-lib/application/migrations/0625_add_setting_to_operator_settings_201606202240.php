<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_setting_to_operator_settings_201606202240 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'custom_withdrawal_processing_stages'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'custom_withdrawal_processing_stages'));
	}
}