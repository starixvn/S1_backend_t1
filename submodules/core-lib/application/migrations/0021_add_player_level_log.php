<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_player_level_log extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'player_level_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_Id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'compared_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'player_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'created_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('player_level_log');
	}

	public function down() {
		$this->dbforge->drop_table('player_level_log');
	}
}
