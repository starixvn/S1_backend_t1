<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_http_request_summary extends CI_Migration {

	protected $tableName = "http_request_summary";

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'cookie' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'user_agent' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'os' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'device' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'is_mobile' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'type' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'createdat' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

			'source_site' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),


		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////////////