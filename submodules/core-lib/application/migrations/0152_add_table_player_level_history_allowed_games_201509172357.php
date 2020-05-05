<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_player_level_history_allowed_games_201509172357 extends CI_Migration {

	private $tableName = 'player_level_history_allowed_games';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'max_bonus' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'history_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
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