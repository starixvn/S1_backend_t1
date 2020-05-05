<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_bonus_bet_amount_to_rtg_game_logs_20181129 extends CI_Migration {

	public function up() {
		$fields = array(
			'bonus_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('rtg_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('rtg_game_logs', 'bonus_bet_amount');
	}
}
