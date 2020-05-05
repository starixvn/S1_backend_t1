<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_player_contact_us_201708291550 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 30,
				'null' => true,
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 30,
				'null' => true,
			),
			'subject' => array(
				'type' => 'VARCHAR',
				'constraint' => 30,
				'null' => true,
			),
			'message' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'other_fields' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => 10,
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('player_contact_us');
	}

	public function down() {
		$this->dbforge->drop_table('player_contact_us');
	}
}
