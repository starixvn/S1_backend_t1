<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agfishing_gamelogs_201610101404 extends CI_Migration {

	private $tableName = 'agfishing_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),

			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'datatype' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'logs_ID' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'transferId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tradeNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'platformType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'playerName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'transferType' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'transferAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'previousAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currentAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'exchangeRate' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'IP' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'flag' => array(
				'type' => 'VARCHAR',
				'constraint' => '5',
				'null' => true,
			),
			'creationTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'remark' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),			
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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