<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_flag_show_in_site_to_game_type_and_game_description extends CI_Migration {

	public function up() {
		$fields = array(
			'flag_show_in_site' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => false,
				'default' => 1,
			),
		);
		$this->dbforge->add_column('game_type', $fields);
		$this->dbforge->add_column('game_description', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_type', 'flag_show_in_site');
		$this->dbforge->drop_column('game_description', 'flag_show_in_site');
	}
}