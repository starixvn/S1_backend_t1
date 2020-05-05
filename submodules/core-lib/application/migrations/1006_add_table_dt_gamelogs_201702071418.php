<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dt_gamelogs_201702071418 extends CI_Migration {

	private $tableName = 'dt_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'dt_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'createTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'playerName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betWins' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'partentId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'prizeWins' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'betLines' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betPrice' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
