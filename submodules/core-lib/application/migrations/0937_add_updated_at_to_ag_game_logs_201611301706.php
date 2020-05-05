<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_updated_at_to_ag_game_logs_201611301706 extends CI_Migration {
	public function up() {

		$this->dbforge->add_column('ag_game_logs', array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));

	}

	public function down() {

		$this->dbforge->drop_column('ag_game_logs', 'updated_at');

	}
}
