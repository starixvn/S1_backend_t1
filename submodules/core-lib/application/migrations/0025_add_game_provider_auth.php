<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_provider_auth extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'game_provider_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'login_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'source' => array(
				'type' => 'INT',
				'null' => true,
			),
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('game_provider_auth');
	}

	public function down() {
		$this->dbforge->drop_table('game_provider_auth');
	}
}
