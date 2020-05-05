<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_enabled_sync_balance_to_game_provider_auth_201512262108 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'enabled_sync_balance' => array(
				'type' => 'INT',
				'default' => 1,
			),
		));

		$this->db->query('create index idx_enabled_sync_balance on game_provider_auth(enabled_sync_balance)');
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'enabled_sync_balance');

		$this->db->query('drop index idx_enabled_sync_balance on game_provider_auth');
	}
}

////END OF FILE//////////////////