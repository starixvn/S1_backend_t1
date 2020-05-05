<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_register_to_game_provider_auth extends CI_Migration {

	public function up() {
		$fields = array(
			'register' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_provider_auth', $fields, 'notes');
	}

	public function down() {
		// $this->db->query('DELETE * FROM `game_provider_auth');
	}
}