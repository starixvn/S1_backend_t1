<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_admin_login_token extends CI_Migration {

	private $tableName = 'admin_login_token';

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
			'admin_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		// $this->dbforge->add_key('token');

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create unique index idx_admin_login_token on ' . $this->tableName . '(token)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
