<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_game_provider_auth_20151005 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'external_account_id' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'external_account_id');
	}
}