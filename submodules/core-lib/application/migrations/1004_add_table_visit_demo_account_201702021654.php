<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_visit_demo_account_201702021654 extends CI_Migration {

	private $tableName = 'visit_demo_account';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'json_data' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
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
