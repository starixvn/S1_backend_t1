<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_Columns_on_sms_verification_2017122616 extends CI_Migration {

	private $tableName = 'sms_verification';

	public function up() {
		$fields = array(
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'response_text' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		);

		 $this->dbforge->add_column($this->tableName, $fields);
		
	}

	public function down() {
		$this->dbforge->drop_column('status', 'response_text');
	}
}
