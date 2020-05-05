<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_agents_201605260304 extends CI_Migration {

	private $tableName = 'agency_agents';
	public function up() {
		$fields = array(
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'parent_id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => false,
				'default' => 0,
			),
		);
        $this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'password');
        $this->dbforge->drop_column($this->tableName, 'parent_id');
	}
}
