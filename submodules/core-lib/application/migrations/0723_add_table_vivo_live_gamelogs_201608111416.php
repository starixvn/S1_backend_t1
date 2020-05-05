<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_vivo_live_gamelogs_201608111416 extends CI_Migration {

	private $tableName = 'vivo_live_game_logs';

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
			'TransactionID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'Playerloginname' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'TransactionDate' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'TransactionType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'TransactionTypeID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'BalanceBefore' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'DebitAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'CreditAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'BalanceAfter' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'TableRoundID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'TableID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'CardProviderID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'CardNumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'LiveDealerGameID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'RNGgameID' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'Currency' => array(
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