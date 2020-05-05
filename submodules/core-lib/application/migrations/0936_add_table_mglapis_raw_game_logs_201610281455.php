<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mglapis_raw_game_logs_201610281455 extends CI_Migration {

	private $tableName = 'mglapis_raw_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'key' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'col_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'mbr_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'mbr_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'player_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'trans_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'trans_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'mgs_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'mgs_action_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'clearing_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'balance_after_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'ref_trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ref_trans_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'record_update_flag' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sync_datetime' => array(
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

		$this->db->query('create index idx_response_result_id on mglapis_raw_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on mglapis_raw_game_logs(external_uniqueid)');
		$this->db->query('create index idx_trans_id on mglapis_raw_game_logs(trans_id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on mglapis_raw_game_logs');
		$this->db->query('drop index idx_external_uniqueid on mglapis_raw_game_logs');
		$this->db->query('drop index idx_trans_id on mglapis_raw_game_logs');

		$this->dbforge->drop_table($this->tableName);
	}
}