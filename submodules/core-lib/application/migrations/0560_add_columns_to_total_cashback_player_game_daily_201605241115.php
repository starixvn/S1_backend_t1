<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_total_cashback_player_game_daily_201605241115 extends CI_Migration {

	public function up() {
		$fields = array(
			'paid_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'paid_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('total_cashback_player_game_daily', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('total_cashback_player_game_daily', 'paid_date');
		$this->dbforge->drop_column('total_cashback_player_game_daily', 'paid_amount');
	}
}