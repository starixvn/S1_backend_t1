<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ibc_game_logs_201604020157 extends CI_Migration {

	private $tableName = 'ibc_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'player_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'transaction_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'league_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'sport_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'away_id_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_id_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'match_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'parlay_refno' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet_team' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'hdp' => array(
				'type' => 'INT',
				'null' => true,
			),
			'away_hdp' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_hdp' => array(
				'type' => 'INT',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'away_score' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_score' => array(
				'type' => 'INT',
				'null' => true,
			),
			'is_live' => array(
				'type' => 'INT',
				'null' => true,
			),
			'ticket_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'stake' => array(
				'type' => 'INT',
				'null' => true,
			),
			'winlose_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'winlost_datetime' => array(
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