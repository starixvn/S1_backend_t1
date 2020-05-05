<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kuma_gamelogs_201612061949 extends CI_Migration {

	private $tableName = 'kuma_game_logs';

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
				'null' => true,
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'BillNo' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => '50',
			),
			'GameID' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => '50',
			),
			'BetValue' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'NetAmount' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'SettleTime' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => '100'
			),
			'AgentsCode' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => '50'
			),
			'Account' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => '50'
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
		$this->dbforge->add_key('Username');
		$this->dbforge->add_key('GameID');

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
