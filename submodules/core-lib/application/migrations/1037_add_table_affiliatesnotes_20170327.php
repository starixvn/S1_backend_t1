<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_affiliatesnotes_20170327 extends CI_Migration {

	private $tableName = 'affiliatenotes';

	public function up() {
		$fields = array(
			'noteId' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'affiliateId' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => false,
			),
			'notes' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'userId' => array(
				'type' => 'INT',
				'constraint' => '10',
			),
			'createdOn' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updatedOn' => array(
				'type' => 'INT',
				'null' => false,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('noteId', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}