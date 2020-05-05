<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201705142130 extends CI_Migration {

	public function up() {
		$fields = array(
			'is_tutorial_done' => array(
				'type' => 'INT',
				'constraint' => '2',
				'default' => 0,
			),

		);

		$this->dbforge->add_column('player', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('player', 'is_tutorial_done');
	}
}
