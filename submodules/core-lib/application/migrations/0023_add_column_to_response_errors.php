<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_response_errors extends CI_Migration {

	public function up() {
		$fields = array(
			'request_api' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'request_params' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'extra' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('response_errors', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('response_errors', 'request_api');
		$this->dbforge->drop_column('response_errors', 'request_params');
		$this->dbforge->drop_column('response_errors', 'extra');
	}
}
