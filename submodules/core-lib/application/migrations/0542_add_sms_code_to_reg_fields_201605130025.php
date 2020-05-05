<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sms_code_to_reg_fields_201605130025 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {
		$this->db->insert($this->tableName, array(
			"type" => '1', "field_name" => 'SMS Verification Code',
			'alias' => '', "visible" => '0', "required" => '0', "updatedOn" => "2016-05-13 00:00:00",
			"can_be_required" => '0'));
	}

	public function down() {
		$this->db->delete($this->tableName, array('field_name' => 'SMS Verification Code'));
	}
}
