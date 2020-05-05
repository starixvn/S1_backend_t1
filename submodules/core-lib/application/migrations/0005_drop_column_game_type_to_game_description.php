<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_column_game_type_to_game_description extends CI_Migration {
	public function up() {
		$this->dbforge->drop_column('game_description', 'game_type');
	}

	public function down() {
		$field = array(
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			));
		$this->dbforge->add_column('game_description', $field, 'game_type_id');
	}
}