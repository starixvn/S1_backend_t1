<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_external_uniqueid_to_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('ag_game_logs', array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
		$this->dbforge->add_column('game_logs', array(
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('ag_game_logs', 'uniqueid');
		$this->dbforge->drop_column('game_logs', 'external_uniqueid');
	}
}