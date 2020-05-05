<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_offline_enabled_to_game_description extends CI_Migration {

	public function up() {
		$fields = array(
			'offline_enabled' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_description', $fields, 'flash_enabled');
	}

	public function down() {
		$this->dbforge->drop_column('game_description', 'offline_enabled');
	}
}