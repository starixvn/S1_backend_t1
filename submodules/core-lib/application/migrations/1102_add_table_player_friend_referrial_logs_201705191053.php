<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_friend_referrial_logs_201705191053 extends CI_Migration {

	private $tableName = 'player_friend_referrial_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'year_month_date' => array(
				'type' => 'INT',
				'null' => false,
			),
			'referred_count' => array(
				'type' => 'INT',
				'null' => false,
			),
			'total_bets' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('ALTER TABLE `player_friend_referrial_logs` ADD UNIQUE `player_friend_referrial_logs_year_month_date`(`player_id`, `year_month_date`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}