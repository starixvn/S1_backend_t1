<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_game_type_id_to_game_description extends CI_Migration {

	public function up() {
		$fields = array(
			'game_type_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => false,
			),
		);
		$this->dbforge->add_column('game_description', $fields, 'game_platform_id');
	}

	public function down() {
		$this->dbforge->drop_column('game_description', 'game_type_id');
	}
}