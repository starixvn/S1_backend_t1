<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gameshortcode_to_gspt_game_logs_201611181530 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('gspt_game_logs', array(
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('gspt_game_logs', 'gameshortcode');
	}
}
