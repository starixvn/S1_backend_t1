<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transfer_request_201611242311 extends CI_Migration {

	private $tableName = 'transfer_request';

	public function up() {
		$fields = array(
			'notes' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'guess_success'=> array(
				'type' => 'INT',
				'default' => 0,
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'notes');
		$this->dbforge->drop_column($this->tableName, 'guess_success');

	}
}
