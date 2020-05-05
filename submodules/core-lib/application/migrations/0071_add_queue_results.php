<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_queue_results extends CI_Migration {

	private $tableName = 'queue_results';

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => false,
			),
			'system_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'func_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'params' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'result' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		// $this->dbforge->add_key('token');

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create unique index idx_queue_results_token on ' . $this->tableName . '(token)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
