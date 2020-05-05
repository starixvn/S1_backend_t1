<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_gameplay_game_logs_201603250650 extends CI_Migration {

	private $tableName = 'gameplay_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'operation_code' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'change_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'change_type' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'ret' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'changes' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'operator' => array(
				'type' => 'VARCHAR(200)',
				'null' => true,
			),
			'jcon' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jwin' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'platform' => array(
				'type' => 'INT',
				'null' => true,
			),
			'ver' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'operation_code');
		$this->dbforge->drop_column($this->tableName, 'change_time');
		$this->dbforge->drop_column($this->tableName, 'change_type');
		$this->dbforge->drop_column($this->tableName, 'game_name');
		$this->dbforge->drop_column($this->tableName, 'ret');
		$this->dbforge->drop_column($this->tableName, 'changes');
		$this->dbforge->drop_column($this->tableName, 'operator');
		$this->dbforge->drop_column($this->tableName, 'jcon');
		$this->dbforge->drop_column($this->tableName, 'jwin');
		$this->dbforge->drop_column($this->tableName, 'platform');
		$this->dbforge->drop_column($this->tableName, 'ver');
	}
}

///END OF FILE//////////