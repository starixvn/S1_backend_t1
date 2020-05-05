<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ipm_game_logs_201705051819 extends CI_Migration {

	public function up() {
		$fields = array(
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('ipm_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ipm_game_logs','player_username');
	}
}