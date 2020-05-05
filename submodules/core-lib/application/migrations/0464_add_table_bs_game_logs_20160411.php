<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_bs_game_logs_20160411 extends CI_Migration {

	private $tableName = 'bs_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE,
			),
			'userId' => array(
				'type' => 'INT',
			),
			'transactionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'gameSessionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'gameId' => array(
				'type' => 'INT',
			),
			'amount' => array(
				'type' => 'INT',
			),
			'totalBet' => array(
				'type' => 'INT',
			),
			'totalWin' => array(
				'type' => 'INT',
			),
			'balance' => array(
				'type' => 'INT',
			),
			'mode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'time' => array(
				'type' => 'TIMESTAMP',
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `bs_game_logs` ADD UNIQUE INDEX (`TRANSACTIONID`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}