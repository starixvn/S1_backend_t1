<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_logs extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'room' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'table' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'rent' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'end_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'external_log_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('game_logs');
	}

	public function down() {
		$this->dbforge->drop_table('game_logs');
	}
}
