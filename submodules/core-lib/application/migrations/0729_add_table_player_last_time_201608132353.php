<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_last_time_201608132353 extends CI_Migration {

	private $tableName = 'player_runtime';

	public function up() {
		$fields=[
			'playerId' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'lastActivityTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'lastLogoutTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'lastLoginTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'lastLoginIp' => array(
				'type' => 'VARCHAR',
				'constraint'=>'60',
				'null' => true,
			),
			'lastLogoutIp' => array(
				'type' => 'VARCHAR',
				'constraint'=>'60',
				'null' => true,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint'=>'100',
				'null' => true,
			),
			'online' => array(
				'type' => 'INT',
				'null' => true,
			),
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('playerId', TRUE);

		$this->dbforge->create_table($this->tableName);

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}