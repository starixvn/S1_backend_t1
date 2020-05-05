<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_lb_game_logs_201512020726 extends CI_Migration {

	private $tableName = 'lb_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'member_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'session_token' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet_no' => array(
				'type' => 'INT',
				'null' => true,
			),
			'match_no' => array(
				'type' => 'INT',
				'null' => true,
			),
			'match_area' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_content' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_money' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_winning' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'trans_time' => array(
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