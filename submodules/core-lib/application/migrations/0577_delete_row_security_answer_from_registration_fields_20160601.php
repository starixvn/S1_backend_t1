<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_delete_row_security_answer_from_registration_fields_20160601 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {
		$this->db->delete($this->tableName, array(
			'registrationFieldId' => 12,
			'field_name' => 'Security Answer',
			'alias' => 'secretAnswer',
		));
	}

	public function down() {
		$this->db->insert($this->tableName, array(
			'registrationFieldId' => 12,
			'field_name' => 'Security Answer',
			'alias' => 'secretAnswer',
		));
	}
	
}