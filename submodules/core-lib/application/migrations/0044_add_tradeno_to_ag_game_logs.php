<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_tradeno_to_ag_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('ag_game_logs', array(
			'tradeno' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('ag_game_logs', 'tradeno');
	}
}