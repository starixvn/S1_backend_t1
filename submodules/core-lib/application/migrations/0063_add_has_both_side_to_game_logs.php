<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_has_both_side_to_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('game_logs', array(
			'has_both_side' => array(
				'type' => 'INT',
				'default' => '1',
				'null' => false,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('game_logs', 'has_both_side');
	}
}
