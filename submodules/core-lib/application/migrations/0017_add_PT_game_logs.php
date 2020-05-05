<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_PT_game_logs extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'gamedate' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'sessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'windowcode' => array(
				'type' => 'INT',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'progressivebet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'progressivewin' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currentbet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'livenetwork' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('pt_game_logs');
	}

	public function down() {
		$this->dbforge->drop_table('pt_game_logs');
	}
}
