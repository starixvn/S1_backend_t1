<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_operator_settings_201605111142 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->insert($this->tableName, array('name' => 'default_3rdparty_payment'));
		// $this->db->insert($this->tableName, array('name' => 'payment_account_types'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('name' => 'default_3rdparty_payment'));
		// $this->db->delete($this->tableName, array('name' => 'payment_account_types'));
	}
}