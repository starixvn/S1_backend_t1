<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_bbin_game_logs_201511161157 extends CI_Migration {

	private $tableName = 'bbin_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'serial_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'round_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'result_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'card' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'exchange_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'commision' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'is_paid' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'modified_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'serial_id');
		$this->dbforge->drop_column($this->tableName, 'round_no');
		$this->dbforge->drop_column($this->tableName, 'game_code');
		$this->dbforge->drop_column($this->tableName, 'result_type');
		$this->dbforge->drop_column($this->tableName, 'card');
		$this->dbforge->drop_column($this->tableName, 'exchange_rate');
		$this->dbforge->drop_column($this->tableName, 'commision');
		$this->dbforge->drop_column($this->tableName, 'is_paid');
		$this->dbforge->drop_column($this->tableName, 'modified_date');
	}
}

///END OF FILE//////////