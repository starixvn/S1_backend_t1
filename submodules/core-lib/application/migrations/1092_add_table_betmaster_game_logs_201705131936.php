<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_betmaster_game_logs_201705131936 extends CI_Migration {

	private $tableName = 'betmaster_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => false,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'round_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => true,
			),
			'real_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'outcome_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'special_odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'subtype_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'is_live' => array(
				'type' => 'ENUM("true","false")',
				'default' => 'false',
				'null' => false,
			),
			'type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_details_date_start' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_details_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'team_home_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'team_away_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'agent_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'payout_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'payout_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'effective_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_finish_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create unique index idx_uniqueid on betmaster_game_logs(uniqueid)');
		$this->db->query('create unique index idx_external_uniqueid on betmaster_game_logs(external_uniqueid)');
		$this->db->query('create unique index idx_response_result_id on betmaster_game_logs(response_result_id)');
		$this->db->query('create index idx_game_details_id on betmaster_game_logs(game_details_id)');
	}

	public function down() {
		$this->db->query('drop index idx_uniqueid on betmaster_game_logs');
		$this->db->query('drop index idx_external_uniqueid on betmaster_game_logs');
		$this->db->query('drop index idx_response_result_id on betmaster_game_logs');
		$this->db->query('drop index idx_game_details_id on betmaster_game_logs');
		$this->dbforge->drop_table($this->tableName);
	}
}