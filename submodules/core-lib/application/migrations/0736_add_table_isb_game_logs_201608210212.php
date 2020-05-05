<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_isb_game_logs_201608210212 extends CI_Migration {

	private $tableName = 'isb_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'sessions' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'sessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '4',
				'null' => true,
			),
			'roundid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'transactionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'jpc' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jpw' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create index idx_response_result_id on isb_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on isb_game_logs(external_uniqueid)');
		$this->db->query('create index idx_id on isb_game_logs(id)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on isb_game_logs');
		$this->db->query('drop index idx_external_uniqueid on isb_game_logs');
		$this->db->query('drop index idx_id on isb_game_logs');
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////