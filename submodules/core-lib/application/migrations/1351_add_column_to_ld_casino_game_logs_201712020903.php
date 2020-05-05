<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ld_casino_game_logs_201712020903 extends CI_Migration {

	private $tableName = 'ld_casino_game_logs';

	public function up() {
		$fields = array(
			'extra' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'extra');
	}
}

////END OF FILE////