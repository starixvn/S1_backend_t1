<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_opuskeno_gamelogs_201611101156 extends CI_Migration {

	private $tableName = 'opus_keno_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'member_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'session_token' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'match_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'match_area' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_content' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
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
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'trans_time' => array(
				'type' => 'DATETIME',
				'null' => false,
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
		$this->dbforge->add_key('player_id');

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
