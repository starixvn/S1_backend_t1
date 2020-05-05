<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_mobile_to_sms_201605181136 extends CI_Migration {

	private $tableName = 'sms_verification';

	public function up() {
		$fields = array(
			'mobile_number' => array(
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => false,
				'default' => '',
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'mobile_number');
	}
}