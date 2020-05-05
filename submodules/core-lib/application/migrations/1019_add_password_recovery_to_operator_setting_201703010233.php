<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_password_recovery_to_operator_setting_201703010233 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		$data = array(
			"name" => 'password_recovery_options' ,
			"value" => '0',
			"note" => 'Each bit of this value defines whether a specific password recovery option is enabled',
		);

		$this->db->insert($this->tableName, $data);
	}

	public function down() {
		$this->db->delete($this->tableName, array('name' => 'password_recovery_options'));
	}
}