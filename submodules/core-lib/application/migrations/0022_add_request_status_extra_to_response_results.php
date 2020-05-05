<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_request_status_extra_to_response_results extends CI_Migration {

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
			'status_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'status_text' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'extra' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('response_results', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('response_results', 'request_api');
		$this->dbforge->drop_column('response_results', 'request_params');
		$this->dbforge->drop_column('response_results', 'status_code');
		$this->dbforge->drop_column('response_results', 'status_text');
		$this->dbforge->drop_column('response_results', 'extra');
	}
}
