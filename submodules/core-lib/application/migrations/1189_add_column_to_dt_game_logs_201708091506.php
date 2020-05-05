<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_dt_game_logs_201708091506 extends CI_Migration {

	private $tableName = 'dt_game_logs';

	public function up() {
		$fields = array(
			'creditAfter' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'creditAfter');
	}
}

////END OF FILE////