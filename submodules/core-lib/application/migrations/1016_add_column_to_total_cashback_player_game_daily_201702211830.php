<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_201702211830 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'original_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			)
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'original_bet_amount');
	}	

}

///END OF FILE//////////