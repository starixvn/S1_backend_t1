<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_total_cashback_player_game_201704261522 extends CI_Migration {

	private $tableName = 'total_cashback_player_game';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'time_start' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'time_end' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'history_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'paid_flag' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'level_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'paid_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'paid_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'cashback_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'withdraw_condition_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'max_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'original_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
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
		$this->dbforge->add_key('time_start');
		$this->dbforge->add_key('time_end');
		$this->dbforge->add_key('player_id');
		$this->dbforge->add_key('game_description_id');

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}