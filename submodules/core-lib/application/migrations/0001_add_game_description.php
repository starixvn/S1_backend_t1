<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_description extends CI_Migration {

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
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'dlc_enabled' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'flash_enabled' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'mobile_enabled' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('game_description');
	}

	public function down() {
		$this->dbforge->drop_table('game_description');
	}
}
