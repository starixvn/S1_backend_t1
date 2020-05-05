<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_31_32_to_registration_fields extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {
		$data = array(
			'registrationFieldId' => 32,
			'type' => '2',
			'field_name' => 'Language',
			'alias' => '',
			'visible' => '0',
			'required' => '1',
			'updatedOn' => '2015-09-04 17:39:14',
			'can_be_required' => '0',
		);
		$this->db->insert($this->tableName, $data);

		// $this->db->query("INSERT INTO `registration_fields` (`registrationFieldId`, `type`, `field_name`, `alias`, `visible`, `required`, `updatedOn`, `can_be_required`)
		// VALUES
		// 	(32, '2', 'Language', '', '0', '1', '2015-09-04 17:39:14', '0');
		// ");
	}

	public function down() {
		$this->db->delete($this->tableName, array('registrationFieldId' => 32));
	}
}