<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_report_logs_201512080510 extends CI_Migration {

	private $tableName = 'report_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			)

		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}