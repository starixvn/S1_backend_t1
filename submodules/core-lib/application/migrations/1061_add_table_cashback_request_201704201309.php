<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_cashback_request_201704201309 extends CI_Migration {

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'secure_id' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'request_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'request_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'processed_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'processed_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'notes' => array(
				'type' => 'TEXT',
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
		$this->dbforge->create_table('cashback_request');

	}

	public function down() {
		$this->dbforge->drop_table('cashback_request');
	}
}