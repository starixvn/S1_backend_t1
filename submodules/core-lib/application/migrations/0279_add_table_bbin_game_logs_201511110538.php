<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_bbin_game_logs_201511110538 extends CI_Migration {

	private $tableName = 'bbin_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'wagers_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'wagers_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'INT',
				'null' => true,
			),
			'payoff' => array(
				'type' => 'INT',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'commisionable' => array(
				'type' => 'INT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'game_platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}