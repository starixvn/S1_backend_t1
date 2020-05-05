<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_after_balance_to_game_logs extends CI_Migration {

	public function up() {
		$fields = array(
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_logs', $fields, 'rent');
	}

	public function down() {
		$this->dbforge->drop_column('game_logs', 'after_balance');
	}
}