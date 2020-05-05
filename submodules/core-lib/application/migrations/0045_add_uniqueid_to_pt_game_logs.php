<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_uniqueid_to_pt_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('pt_game_logs', array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('pt_game_logs', 'uniqueid');
	}
}
