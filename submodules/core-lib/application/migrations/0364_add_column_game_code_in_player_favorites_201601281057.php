<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_game_code_in_player_favorites_201601281057 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('player_favorites', array(
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('player_favorites', 'game_code');
	}
}

///END OF FILE//////////