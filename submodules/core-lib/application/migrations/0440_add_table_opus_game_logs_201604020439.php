<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_opus_game_logs_201604020439 extends CI_Migration {

	private $tableName = 'opus_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'trans_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'member_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'member_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '3',
				'null' => true,
			),
			'balance_start' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'balance_end' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'game_detail' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'bet_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'player_hand' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'game_result' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'game_category' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'vendor' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'draw_number' => array(
				'type' => 'INT',
				'null' => true,
			),
			'm88_studio' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'stamp_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bet_record_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'game_platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
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
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}