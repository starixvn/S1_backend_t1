<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_bbin_game_logs_201602102107 extends CI_Migration {

	private $tableName = 'bbin_game_logs';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'game_kind' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'updated_at');
		$this->dbforge->drop_column($this->tableName, 'game_kind');
	}
}

///END OF FILE//////////