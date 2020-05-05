<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_provider_auth_201806032307 extends CI_Migration {

	public function up() {
		$fields = array(
			'additional' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);

		$this->dbforge->add_column('game_provider_auth', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_provider_auth', 'additional');
	}
}
