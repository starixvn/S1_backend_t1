<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agbbin_game_logs_201610060338 extends CI_Migration {

	private $tableName = 'agbbin_game_logs';

	public function up() {
		$fields = array(

			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'creationtime' => array(
				'type' => 'DATETIME',
				'null' => true,
				),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
				),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
				),
			);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'response_result_id');
		$this->dbforge->drop_column($this->tableName, 'uniqueid');
		$this->dbforge->drop_column($this->tableName, 'creationtime');
		$this->dbforge->drop_column($this->tableName, 'external_uniqueid');
		$this->dbforge->drop_column($this->tableName, 'response_result_id');
	}
}
