<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_provider_auth_201609100506 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$fields = array(
			'is_demo_flag' => array(
				'type' => 'INT',
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'is_demo_flag');
	}

}