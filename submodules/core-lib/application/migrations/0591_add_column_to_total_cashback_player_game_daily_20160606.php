<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_20160606 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'cashback_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'cashback_percentage');
		$this->dbforge->drop_column($this->tableName, 'bet_amount');
	}
}