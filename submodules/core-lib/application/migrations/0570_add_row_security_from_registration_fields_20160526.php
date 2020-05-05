<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_security_from_registration_fields_20160526 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {
		$this->db->insert($this->tableName, array(
			'registrationFieldId' => 11,
			'field_name' => 'Security Question',
			'alias' => 'secretQuestion',
		));

		$this->db->insert($this->tableName, array(
			'registrationFieldId' => 12,
			'field_name' => 'Security Answer',
			'alias' => 'secretAnswer',
		));
	}

	public function down() {
		$this->db->delete($this->tableName, array(
			'registrationFieldId' => 11,
			'field_name' => 'Security Question',
			'alias' => 'secretQuestion',
		));

		$this->db->delete($this->tableName, array(
			'registrationFieldId' => 12,
			'field_name' => 'Security Answer',
			'alias' => 'secretAnswer',
		));
	}
}