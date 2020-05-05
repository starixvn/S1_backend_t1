<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_updated_at_to_game_logs extends CI_Migration {

	private $tableName = 'game_logs';

	public function up() {
		$fields = array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'updated_at');
	}
}
///END OF FILE/////////////