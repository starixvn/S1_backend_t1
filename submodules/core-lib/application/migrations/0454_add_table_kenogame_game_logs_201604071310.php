<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kenogame_game_logs_201604071310 extends CI_Migration {

	private $tableName = 'kenogame_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'BetId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'BetType' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'RegionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'BetSlip' => array(
				 'type' => 'VARCHAR',
				 'constraint' => '1000',
                 'null' => TRUE,
			),
			'Odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'StakeAccurate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Credit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'PJackpot' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'CreateTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

			'UpdateTime' => array(
				'type' => 'DATETIME',
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