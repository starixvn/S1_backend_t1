<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_concurrency_control_201707131430 extends CI_Migration {

	private $tableName = 'concurrency_control';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
            'lock_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
			'created_at' => array(
				'type' => 'DATETIME',
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
