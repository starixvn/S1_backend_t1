<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gspt_game_logs_201603092031 extends CI_Migration {

	private $tableName = 'gspt_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'window_code' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),

			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'progressive_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'progressive_win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'current_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),

			'game_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

			'info' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),

			'live_network' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),

			'r_num' => array(
				'type' => 'INT',
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