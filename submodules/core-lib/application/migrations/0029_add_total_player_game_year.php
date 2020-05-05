<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_total_player_game_year extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'betting_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'year' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('total_player_game_year');
	}

	public function down() {
		$this->dbforge->drop_table('total_player_game_year');
	}
}
