<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_qt_game_logs_201605030548 extends CI_Migration {

	private $tableName = 'qt_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'transId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'totalBet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'totalPayout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'initiated' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'completed' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'operatorId' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'playerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'device' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'gameProvider' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameCategory' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameClientType' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
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