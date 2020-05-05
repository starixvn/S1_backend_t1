<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_column_description_from_new_player_tutorial_201705121952 extends CI_Migration {

	public function up() {
		$this->dbforge->drop_column('new_player_tutorial', 'description');
	}

	public function down() {
		$fields = array(
			'description' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('new_player_tutorial', $fields);
	}
}