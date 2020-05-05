<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_block_to_game_provider_auth_20151021 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'blockedStart' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'blockedEnd' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'last_sync_at' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),

		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'blockedStart');
		$this->dbforge->drop_column($this->tableName, 'blockedEnd');
		$this->dbforge->drop_column($this->tableName, 'last_sync_at');
	}
}