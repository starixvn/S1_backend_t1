<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_reset_to_player_20151019 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'resetCode' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			),
			'resetExpire' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'resetCode');
		$this->dbforge->drop_column($this->tableName, 'resetExpire');
	}
}