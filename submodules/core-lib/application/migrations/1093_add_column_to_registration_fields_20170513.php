<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_registration_fields_20170513 extends CI_Migration {

	public function up() {
		$fields = array(
			'account_visible' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'account_required' => array(
				'type' => 'INT',
				'default' => 0,
			),
		);

		$this->dbforge->add_column('registration_fields', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('registration_fields', 'account_visible');
		$this->dbforge->drop_column('registration_fields', 'account_required');
	}
}
