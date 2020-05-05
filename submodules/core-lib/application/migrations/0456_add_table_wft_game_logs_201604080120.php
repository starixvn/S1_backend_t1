<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_wft_game_logs_201604080120 extends CI_Migration {

	private $tableName = 'wft_game_logs';

	# Reference: Ticket Info - 2013-04-29.doc
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'fetch_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'ticket_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'last_modified' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'user_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'win_amount' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'commission_amount' => array( # this might be 'commissionable_amount?'
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'commission_rate' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'bet_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'danger_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'side' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'info' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'half' => array(
				'type' => 'BIT',
				'null' => true,
			),
			'trans_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'work_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'match_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'run_score' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'score' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'ht_score' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'first_last_goal' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'game_desc' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'game_result' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'exchange_rate' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,4',
				'null' => true,
			),
			'jp' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'odds_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'sports_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
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