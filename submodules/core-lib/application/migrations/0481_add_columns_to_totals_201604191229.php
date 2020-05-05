<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_totals_201604191229 extends CI_Migration {

	private $tables = array(
		'total_player_game_minute',
		'total_operator_game_minute',
	);

	public function up() {
		$columns = array(
			'win_amount' => array(
				'type' => 'double',
				'null' => true,
			),
			'loss_amount' => array(
				'type' => 'double',
				'null' => true,
			),
			'date_minute' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);

		foreach ($this->tables as $tableName) {
			$this->dbforge->add_column($tableName, $columns);
			$this->db->query('create unique index idx_uniqueid on ' . $tableName . '(uniqueid)');
		}

	}

	public function down() {
		foreach ($this->tables as $tableName) {
			$this->dbforge->drop_column($tableName, 'win_amount');
			$this->dbforge->drop_column($tableName, 'loss_amount');
			$this->dbforge->drop_column($tableName, 'date_minute');
			$this->dbforge->drop_column($tableName, 'game_platform_id');
			$this->dbforge->drop_column($tableName, 'game_type_id');
			$this->dbforge->drop_column($tableName, 'game_description_id');
			$this->dbforge->drop_column($tableName, 'uniqueid');
		}
	}
}