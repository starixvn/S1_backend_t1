<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_merchants_201706161623 extends CI_Migration {

	private $tableName = 'merchants';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'merchant_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'merchant_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'live_mode' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'staging_secure_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'staging_sign_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'live_secure_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'live_sign_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('created_at');

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
