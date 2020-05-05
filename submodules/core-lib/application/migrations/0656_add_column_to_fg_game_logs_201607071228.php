<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_fg_game_logs_201607071228 extends CI_Migration {
	private $tableName = 'fg_game_logs';
	public function up() {
		$fields = array(
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'platform_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'platform_tran_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'result_amount');
		$this->dbforge->drop_column($this->tableName, 'platform_code');
		$this->dbforge->drop_column($this->tableName, 'platform_tran_id');
	}
}
