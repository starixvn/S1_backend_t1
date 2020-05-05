<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_response_results extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'system_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'content' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'filepath' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('response_results');
	}

	public function down() {
		$this->dbforge->drop_table('response_results');
	}
}
