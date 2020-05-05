<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_201707232241 extends CI_Migration {

	private $tableName = 'total_cashback_player_game';

	public function up() {
		$fields = array(
			'diff_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'diff_paid_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'diff_cashback_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'diff_bet_amount');
		$this->dbforge->drop_column($this->tableName, 'diff_paid_amount');
		$this->dbforge->drop_column($this->tableName, 'diff_cashback_amount');
	}
}

////END OF FILE////