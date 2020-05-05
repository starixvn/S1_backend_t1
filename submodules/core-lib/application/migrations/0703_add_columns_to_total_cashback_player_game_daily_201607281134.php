<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_total_cashback_player_game_daily_201607281134 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {
		$fields = array(
			'withdraw_condition_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'withdraw_condition_amount');
	}
}
