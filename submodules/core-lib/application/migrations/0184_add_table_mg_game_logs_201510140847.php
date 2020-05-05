<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mg_game_logs_201510140847 extends CI_Migration {

	private $tableName = 'mg_game_logs';

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
			),
			'row_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'display_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'display_game_category' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'session_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_end_time' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			"total_wager" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"total_payout" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"progressive_wage" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"iso_code" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"game_platform" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			"external_uniqueid" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			"response_result_id" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}