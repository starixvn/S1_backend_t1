<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_totals_201601241509 extends CI_Migration {

	private $tables = array(
		'total_player_game_hour',
		'total_player_game_day',
		'total_player_game_month',
		'total_player_game_year',
		'total_operator_game_hour',
		'total_operator_game_day',
		'total_operator_game_month',
		'total_operator_game_year',
		'game_logs',
	);

	public function up() {
		$columns = array(
			'win_amount' => array(
				'type' => 'double',
				'null' => true,
			),
			'loss_amount' => array(
				'type' => 'double',
				'null' => true,
			),
		);

		foreach ($this->tables as $tableName) {
			$this->dbforge->add_column($tableName, $columns);
		}

	}

	public function down() {
		foreach ($this->tables as $tableName) {
			$this->dbforge->drop_column($tableName, 'win_amount');
			$this->dbforge->drop_column($tableName, 'loss_amount');
		}
	}
}