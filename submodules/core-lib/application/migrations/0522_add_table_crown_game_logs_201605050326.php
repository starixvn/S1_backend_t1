<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_crown_game_logs_201605050326 extends CI_Migration {

	private $tableName = 'crown_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'bet_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'account' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_odd' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'comm' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'oddstyle' => array(
				'type' => 'VARCHAR',
				'constraint' => '1',
				'null' => true,
			),
			'hdp' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_pos' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'live' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'bet_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'report_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'sport_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet_score' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'match_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bet_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'update_time' => array(
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

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create index idx_response_result_id on crown_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on crown_game_logs(external_uniqueid)');
		$this->db->query('create index idx_bet_id on crown_game_logs(bet_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on crown_game_logs');
		$this->db->query('drop index idx_external_uniqueid on crown_game_logs');
		$this->db->query('drop index idx_bet_id on crown_game_logs');
		$this->dbforge->drop_table($this->tableName);
	}
}