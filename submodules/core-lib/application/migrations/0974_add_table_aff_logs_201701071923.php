<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_aff_logs_201701071923 extends CI_Migration {

	public function up() {

		$db_true=1;

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'management' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			),
			'action' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			),
			'description' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => true,
			),
			'referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			),
			'uri' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			),
			'data' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => $db_true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('ip');
		$this->dbforge->add_key('updated_at');
		$this->dbforge->add_key('uri');

		$this->dbforge->create_table('aff_logs');

	}

	public function down() {

		$this->dbforge->drop_table('aff_logs');

	}
}
