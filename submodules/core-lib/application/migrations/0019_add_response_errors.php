<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_response_errors extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'system_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id1' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id2' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id3' => array(
				'type' => 'INT',
				'null' => true,
			),
			'status_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'status_text' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'content' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('response_errors');
	}

	public function down() {
		$this->dbforge->drop_table('response_errors');
	}
}
