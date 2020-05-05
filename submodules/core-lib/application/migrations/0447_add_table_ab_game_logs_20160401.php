<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ab_game_logs_20160401 extends CI_Migration {

	private $tableName = 'ab_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE,
			),
			'client' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'betNum' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'gameRoundId' => array(
				'type' => 'VARCHAR',
				'constraint' => '9',
			),
			'gameType' => array(
				'type' => 'INT',
			),
			'betTime' => array(
				'type' => 'TIMESTAMP',
			),
			'betAmount' => array(
				'type' => 'DOUBLE',
			),
			'validAmount' => array(
				'type' => 'DOUBLE',
			),
			'winOrLoss' => array(
				'type' => 'DOUBLE',
			),
			'state' => array(
				'type' => 'INT',
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'exchangeRate' => array(
				'type' => 'DOUBLE',
			),
			'betType' => array(
				'type' => 'INT',
			),
			'gameResult' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
			),
			'gameRoundEndTime' => array(
				'type' => 'TIMESTAMP',
			),
			'gameRoundStartTime' => array(
				'type' => 'TIMESTAMP',
			),
			'tableName' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'commission' => array(
				'type' => 'INT',
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `ab_game_logs` ADD UNIQUE INDEX (`gameRoundId`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}