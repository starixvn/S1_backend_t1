<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_hrcc_game_logs_201609290259 extends CI_Migration {

	private $tableName = 'hrcc_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'race_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'race_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'race_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'race_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'runner_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'limit' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'transaction_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'update_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'place' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'win_flag' => array(
				'type' => 'INT',
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

		$this->db->query('create index idx_response_result_id on hrcc_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on hrcc_game_logs(external_uniqueid)');
		$this->db->query('create index idx_trans_id on hrcc_game_logs(trans_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_trans_id on hrcc_game_logs');
		$this->db->query('drop index idx_external_uniqueid on hrcc_game_logs');
		$this->db->query('drop index idx_trans_id on hrcc_game_logs');

		$this->dbforge->drop_table($this->tableName);
	}
}