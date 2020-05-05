<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_created_at_to_mg_game_logs_201510142302 extends CI_Migration {

	private $tableName = 'mg_game_logs';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'created_at');
	}
}
