<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_date_to_daily_balance extends CI_Migration {

	private $tableName = 'daily_balance';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'game_date' => array(
				'type' => 'DATE',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'game_date');
	}
}
///END OF FILE