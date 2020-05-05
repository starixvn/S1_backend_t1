<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_gameplay_game_logs_201603240754 extends CI_Migration {

	private $tableName = 'gameplay_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'game_code' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'game_code');
	}
}

///END OF FILE//////////