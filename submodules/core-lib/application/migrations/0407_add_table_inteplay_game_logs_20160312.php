<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_inteplay_game_logs_20160312 extends CI_Migration {

	private $tableName = 'inteplay_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE,
			),
			'code' => array(
				'type' => 'INT',
				'null' => FALSE,
			),
			'totalBet' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'totalPay' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'totalWinLose' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'date' => array(
				'type' => 'DATE',
				'null' => FALSE,
			),
			'grossWin' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'income' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => FALSE,
			),
			'playname' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => FALSE,
			),
			'operator' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => FALSE,
			),
			'gameKey' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => FALSE,
			),
			'gameSetKey' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => FALSE,
			),
			'createTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => FALSE,
			),
			'createTimeStr' => array(
				'type' => 'TIMESTAMP',
				'null' => FALSE,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `inteplay_game_logs` ADD UNIQUE INDEX (`gameSetKey`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}