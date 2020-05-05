<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ttg_game_logs_20160603 extends CI_Migration {
	private $tableName = 'ttg_game_logs';

	public function up() {
		$fields = array(
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet');
		$this->dbforge->drop_column($this->tableName, 'start_at');
	}

}