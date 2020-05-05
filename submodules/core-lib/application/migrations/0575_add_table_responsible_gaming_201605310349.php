<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_responsible_gaming_201605310349 extends CI_Migration {

	private $tableName = 'responsible_gaming';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'time_by_min' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'time_by_hour' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'period_cnt' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'period_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'date_from' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'date_to' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'game_provider' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'remarks' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'admin_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}