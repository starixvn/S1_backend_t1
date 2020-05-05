<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_t1games_game_logs_201710051840 extends CI_Migration {

	private $tableName = 't1games_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'uniqueid' => array(
				'type' => 'INT',
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'merchant_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'game_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'game_finish_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'payout_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'round_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'real_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'effective_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payout_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
