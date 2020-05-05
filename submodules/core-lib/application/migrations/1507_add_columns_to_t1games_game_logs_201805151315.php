<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_t1games_game_logs_201805151315 extends CI_Migration {

	private $tableName = 't1games_game_logs';

	public function up() {

		$fields = array(
			'game_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),

		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'game_status');
	}
}
