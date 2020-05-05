<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_entwine_game_logs_201606130733 extends CI_Migration {

	private $tableName = 'entwine_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'deal_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'deal_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'deal_status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'deal_startdate' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'deal_enddate' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'payout_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'hold' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'handle' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'deal_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'results' => array(
				'type' => 'TEXT',
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

		$this->db->query('create index idx_response_result_id on entwine_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on entwine_game_logs(external_uniqueid)');
		$this->db->query('create index idx_deal_id on entwine_game_logs(deal_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on entwine_game_logs');
		$this->db->query('drop index idx_external_uniqueid on entwine_game_logs');
		$this->db->query('drop index idx_deal_id on entwine_game_logs');
		$this->dbforge->drop_table($this->tableName);
	}
}