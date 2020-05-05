<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_withdraw_password_to_registration_fields_201601111920 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {
		$this->db->trans_start();
			$this->db->insert($this->tableName, array(
				'registrationFieldId' => 35,
				'field_name' => 'Withdrawal Password',
				'alias' => 'withdrawPassword',
			));
		$this->db->trans_complete();
	}

	public function down() {

		$this->db->delete($this->tableName, array(
			'registrationFieldId' => 35,
			'field_name' => 'Withdrawal Password',
			'alias' => 'withdrawPassword',
		));
	}
}