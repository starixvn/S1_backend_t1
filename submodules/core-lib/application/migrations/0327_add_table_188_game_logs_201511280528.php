<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_188_game_logs_201511280528 extends CI_Migration {

	private $tableName = 'one88_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'user_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'wagers_no' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'date_created' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'member_ip_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'odds_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'handicap' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_stakef' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'period_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'sport_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'competition_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'event_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'event_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'date_event' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'score_home' => array(
				'type' => 'INT',
				'null' => true,
			),
			'score_away' => array(
				'type' => 'INT',
				'null' => true,
			),
			'selection_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'settlement_status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'winloss_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'void_reason' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
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