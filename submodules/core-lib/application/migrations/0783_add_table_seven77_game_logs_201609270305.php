<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_seven77_game_logs_201609270305 extends CI_Migration {

	private $tableName = 'seven77_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'agent' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_system' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_session' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_bet_count' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_bet_refund' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_bet_refund_count' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_paid' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_paid_count' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_paid_refund' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'game_paid_refund_count' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_paid' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_profit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'end_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'details' => array(
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

		$this->db->query('create index idx_response_result_id on seven77_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on seven77_game_logs(external_uniqueid)');
		$this->db->query('create index idx_result_id on seven77_game_logs(result_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on seven77_game_logs');
		$this->db->query('drop index idx_external_uniqueid on seven77_game_logs');
		$this->db->query('drop index idx_result_id on seven77_game_logs');

		$this->dbforge->drop_table($this->tableName);
	}
}