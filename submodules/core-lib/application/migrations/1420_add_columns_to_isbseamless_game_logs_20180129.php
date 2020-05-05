<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_isbseamless_game_logs_20180129 extends CI_Migration {

	private $tableName = 'isbseamless_game_logs';

	public function up() {
		$fields = array(
			'sessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => 48,
				'null' => true,
			),
			'skinid' => array(
				'type' => 'VARCHAR',
				'constraint' => 32,
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'sessionid');
		$this->dbforge->drop_column($this->tableName, 'skinid');
	}

}