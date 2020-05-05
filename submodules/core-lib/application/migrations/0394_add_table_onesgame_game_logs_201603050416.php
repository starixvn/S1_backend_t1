<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_onesgame_game_logs_201603050416 extends CI_Migration {

	private $tableName = 'onesgame_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'player_currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'game_category' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'tran_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'total_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jackpot_contribution' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
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