<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cashback_rules_201610280418 extends CI_Migration {

	private $tableName = "common_cashback_rules";

	public function up() {
		$fields = array(
			'created_by' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'default_percentage' => array(
				'type' => 'DOUBLE',
				'null' => TRUE,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'null' => TRUE,
				'constraint' => 2,
				'default' => 0,
			),
			'json_info' => array(
				'type' => 'VARCHAR',
				'null' => TRUE,
				'constraint' => 300,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'json_info');
		$this->dbforge->drop_column($this->tableName, 'created_by');
		$this->dbforge->drop_column($this->tableName, 'updated_by');
		$this->dbforge->drop_column($this->tableName, 'default_percentage');
		$this->dbforge->drop_column($this->tableName, 'status');
	}
}
