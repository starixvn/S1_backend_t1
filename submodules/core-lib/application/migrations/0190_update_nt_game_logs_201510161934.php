<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_nt_game_logs_201510161934 extends CI_Migration {

	private $tableName = 'nt_game_logs';

	public function up() {
		$fields = array(
			'symbol' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
		$fields = array(
			'symbol' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}