<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_mg_game_logs_201510151121 extends CI_Migration {

	private $tableName = 'mg_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => FALSE,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}