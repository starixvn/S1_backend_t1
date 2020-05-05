<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_impt_game_logs_20160420 extends CI_Migration {

	private $tableName = 'impt_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'auto_increment' => TRUE,
				'null' => false
			),
			'PlayerName' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'WindowCode' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
			'GameId' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
			'GameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'GameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'GameName' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'SessionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'Bet' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'Win' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'ProgressiveBet' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'ProgressiveWin' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'Balance' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'CurrentBet' => array(
				'type' => 'DOUBLE',
                'null' => TRUE,
			),
			'GameDate' => array(
				'type' => 'TIMESTAMP',
                'null' => TRUE,
			),
			'LiveNetwork' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
                'null' => TRUE,
			),
			'RNum' => array(
				'type' => 'INT',
                'null' => TRUE,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `impt_game_logs` ADD UNIQUE INDEX (`GameCode`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}