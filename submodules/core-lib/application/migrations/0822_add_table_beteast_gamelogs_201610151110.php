<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_beteast_gamelogs_201610151110 extends CI_Migration {

	private $tableName = 'beteast_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'no' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_cash' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win_cash' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_player' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_banker' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_player_pair' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_banker_pair' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_super_six' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_tie' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'card_player' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'card_banker' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_dt' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'end_dt' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'room_idx' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'super_six' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_log' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'login_nm' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'agent_nm' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
