<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_verified_phone_to_player_20160519 extends CI_Migration {

	public function up() {
		$fields = array(
			'verified_phone' => array(
				'type' => 'INT',
				'constraint' => '1',
				'null' => false,
				'default' => '0',
			),
		);
		$this->dbforge->add_column('player', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('player', 'verified_phone');
	}
}