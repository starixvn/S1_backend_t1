<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_bet_details_to_t1games_game_logs extends CI_Migration {

	public function up() {
		$fields = array(
			'bet_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('t1games_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('t1games_game_logs', 'bet_details');
	}
}