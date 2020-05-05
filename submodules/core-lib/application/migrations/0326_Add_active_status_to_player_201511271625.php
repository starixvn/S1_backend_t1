<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_active_status_to_player_201511271625 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		//default is active
		$this->dbforge->add_column($this->tableName, array(
			'active_status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		));

		$this->db->query('create index idx_active_status on player(active_status)');

	}

	public function down() {
		$this->db->query('drop index idx_active_status on player');

		$this->dbforge->drop_column($this->tableName, 'active_status');
	}
}