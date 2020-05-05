<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_total_cashback_player_game_daily_201509181217 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

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
			'total_date' => array(
				'type' => 'DATE',
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