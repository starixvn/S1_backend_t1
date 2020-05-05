<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_sync_status_201511291507 extends CI_Migration {

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
			'func' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'start_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'end_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'from_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'to_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->add_key('game_platform_id');
		$this->dbforge->add_key('start_at');
		$this->dbforge->add_key('end_at');
		$this->dbforge->add_key('status');

		$this->dbforge->create_table('sync_status');
	}

	public function down() {
		$this->dbforge->drop_table('sync_status');
	}
}
