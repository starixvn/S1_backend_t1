<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_player_level_history_201509141438 extends CI_Migration {

	private $tableName = 'player_level_history';

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'old_level_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'total_betting_amount' => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			'cashback_amount' => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			"status" => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		//player_level_history_game_api_details
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
			),
			"history_id" => array(
				'type' => 'INT',
				'null' => false,
			),
			"game_platform_id" => array(
				'type' => 'INT',
				'null' => false,
			),
			"max_bonus" => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			"percentage" => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			"status" => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('player_level_history_game_api_details');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
		$this->dbforge->drop_table('player_level_history_game_api_details');
	}
}