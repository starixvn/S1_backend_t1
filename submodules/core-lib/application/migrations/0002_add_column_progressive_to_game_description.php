<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_progressive_to_game_description extends CI_Migration {

	public function up() {
		$fields = array(
			'progressive' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_description', $fields, 'dlc_enabled');
	}

	public function down() {
		$this->dbforge->drop_column('game_description', 'progressive');
	}
}