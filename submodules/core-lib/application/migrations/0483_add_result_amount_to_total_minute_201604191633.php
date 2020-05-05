<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_result_amount_to_total_minute_201604191633 extends CI_Migration {

	private $tables = array(
		'total_player_game_minute',
		'total_operator_game_minute',
	);

	public function up() {
		$columns = array(
			'result_amount' => array(
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
			$this->dbforge->drop_column($tableName, 'result_amount');
		}
	}
}