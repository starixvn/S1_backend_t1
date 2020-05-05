<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_is_online_to_game_provider_auth_201708121840 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			"is_online" => array(
				'type' => 'INT',
				'null' => false,
				'default' => '0',
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'is_online');
	}
}