<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_operator_settings_201606010457 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'responsible_gaming','value' => false));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'responsible_gaming'));
	}
}