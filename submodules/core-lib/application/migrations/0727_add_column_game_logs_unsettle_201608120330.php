<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_game_logs_unsettle_201608120330 extends CI_Migration {
	public function up() {
		$fields = array(
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
		);
		$this->dbforge->add_column('game_logs_unsettle', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_logs_unsettle', 'after_balance');
	}
}
