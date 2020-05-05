<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_universal_savestate_201708101813 extends CI_Migration {

	private $tableName = 'universal_savestate';

	public function up() {
		$fields = array(
			'universalsavestateid' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'datatablename' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'columnshownumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'columnhidenumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('universalsavestateid', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
