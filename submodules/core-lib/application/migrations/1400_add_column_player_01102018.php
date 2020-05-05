<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_player_01102018 extends CI_Migration {

	public function up() {
		$fields = array(
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '5',
				'null' => true,
			),
		);

		$this->dbforge->add_column('player', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('player', 'currency');
	}
}