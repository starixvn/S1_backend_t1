<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_created_at_and_updated_at_to_queue_results extends CI_Migration {

	private $tableName = 'queue_results';

	public function up() {
		$fields = array(
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'created_at');
		$this->dbforge->drop_column($this->tableName, 'updated_at');
	}
}