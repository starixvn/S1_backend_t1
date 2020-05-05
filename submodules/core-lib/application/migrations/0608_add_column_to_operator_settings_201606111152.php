<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_operator_settings_201606111152 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'special_payment_list'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'special_payment_list'));
	}
}