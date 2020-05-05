<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add__to_game_provider_auth_201510181622 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'is_blocked' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'is_blocked');
	}
}

////END OF FILE//////////////////