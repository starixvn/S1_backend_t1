<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_uc_gamelogs_201610241739 extends CI_Migration {

	private $tableName = 'uc_game_logs';

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
			'TicketId' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'ExtTransactionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'WinAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'ValidAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'CreatedDate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'TimeStamp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'GameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'RoundId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'CurrencyCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'ExternalId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'Multiplier' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
