<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_result_amount_column_to_total_player_game_day_20151116 extends CI_Migration {

	private $tableName = 'total_player_game_day';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'result_amount' => array(
				'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.00,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'result_amount');
	}
}

///END OF FILE//////////