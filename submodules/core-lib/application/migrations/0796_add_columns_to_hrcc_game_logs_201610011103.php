<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_hrcc_game_logs_201610011103 extends CI_Migration {

	private $tableName = 'hrcc_game_logs';

	public function up() {
		$fields = array(
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet_amount');
	}
}
