<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 */
class Migration_Add_paid_flag_to_total_cashback_player_game_daily_201510242048 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {

		$fields = array(
			'paid_flag' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'paid_flag');
	}
}

///END OF FILE/////