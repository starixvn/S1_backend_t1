<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_new_ebet_mg_game_logs_121520171450 extends CI_Migration {

	private $tableName = 'ebetmg_game_logs';

	public function up() {
		#delete first existing table
		$this->dbforge->drop_table($this->tableName);

		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE
			),
			'row_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
				'null' => true,
			),
			'game_end_time' => array(
				'type' => 'DATETIME',
				'null' => true,
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
			"module_id" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			"client_id" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			"transaction_id" => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			"pca" => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			"tag" => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			"third_party" => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			"uniqueid" => array(
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