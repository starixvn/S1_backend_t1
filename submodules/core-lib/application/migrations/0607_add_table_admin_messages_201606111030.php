<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_admin_messages_201606111030 extends CI_Migration {

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'from_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'content' => array(
				'type' => 'TEXT',
				'null' => false,
			),
			'options' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 3,
			),
			'deleted_at' => array(
				'type' => 'DATETIME',
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
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('admin_messages');

	}

	public function down() {
		$this->dbforge->drop_table('admin_messages');
	}
}