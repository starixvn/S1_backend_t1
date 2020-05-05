<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gsmg_game_logs_201609290259 extends CI_Migration {

	private $tableName = 'gsmg_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'row_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'display_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'display_game_category' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_end_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'total_wager' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_payout' => array(
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
			'progressive_wage' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'iso_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'module_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'client_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'pca' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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

		$this->db->query('create index idx_response_result_id on gsmg_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on gsmg_game_logs(external_uniqueid)');
		$this->db->query('create index idx_row_id on gsmg_game_logs(row_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_row_id on gsmg_game_logs');
		$this->db->query('drop index idx_external_uniqueid on gsmg_game_logs');
		$this->db->query('drop index idx_row_id on gsmg_game_logs');

		$this->dbforge->drop_table($this->tableName);
	}
}