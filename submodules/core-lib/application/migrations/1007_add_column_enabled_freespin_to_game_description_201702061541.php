<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_enabled_freespin_to_game_description_201702061541 extends CI_Migration {

	public function up() {
		$fields = array(
			'enabled_freespin' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_description', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_description', 'enabled_freespin');
	}
}