<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_game_platform_rate_201511240144 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('external_system', array(
			'game_platform_rate' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));

		$this->dbforge->add_column('external_system_list', array(
			'game_platform_rate' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('external_system', 'game_platform_rate');
		$this->dbforge->drop_column('external_system_list', 'game_platform_rate');
	}
}

///END OF FILE//////////