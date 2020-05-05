<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_habanero_gamelogs_201802201707 extends CI_Migration {
	public function up() {
		$fields = array(
			'BonusToReal' => array(
				'type' => 'double',
				'null' => true
			),
		);
		$this->dbforge->add_column('haba88_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('haba88_game_logs', 'BonusToReal');
	}
}
