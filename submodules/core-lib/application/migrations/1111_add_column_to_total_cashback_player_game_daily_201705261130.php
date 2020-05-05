<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_201705261130 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'cashback_request_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			)
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'cashback_request_amount');
	}	

}

///END OF FILE//////////