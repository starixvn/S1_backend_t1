<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_gameplay_game_logs_201603241150 extends CI_Migration {

	private $tableName = 'gameplay_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'bet_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bundle_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_result' => array(
				'type' => 'VARCHAR(400)',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR(10)',
				'null' => true,
			),
			'player_hand' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'table_id' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'lucky_num' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet_id');
		$this->dbforge->drop_column($this->tableName, 'bundle_id');
		$this->dbforge->drop_column($this->tableName, 'game_result');
		$this->dbforge->drop_column($this->tableName, 'currency');
		$this->dbforge->drop_column($this->tableName, 'player_hand');
		$this->dbforge->drop_column($this->tableName, 'table_id');
		$this->dbforge->drop_column($this->tableName, 'game_id');
		$this->dbforge->drop_column($this->tableName, 'lucky_num');
	}
}

///END OF FILE//////////