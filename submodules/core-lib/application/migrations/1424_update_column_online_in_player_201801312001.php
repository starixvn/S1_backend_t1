<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_online_in_player_201801312001 extends CI_Migration {
	private $tableName = 'player';

	public function up() {
		//modify column
		$fields = array(
			'online' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
		$fields = array(
			'online' => array(
				'type' => 'enum("0","1")',
				'default' => '1',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}