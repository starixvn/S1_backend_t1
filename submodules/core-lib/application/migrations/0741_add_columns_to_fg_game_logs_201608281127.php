<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_fg_game_logs_201608281127 extends CI_Migration {

	private $tableName = 'fg_game_logs';

	public function up() {
		$fields = array(
			'win_flag' => array(
				'type' => 'INT',
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'win_flag');
	}

}