<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ttg_game_logs_20160506 extends CI_Migration {

	private $tableName = 'ttg_game_logs';
	private $username_col = 'playerId';
	private $timestamp_col = 'transactionDate';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'partnerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'handId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'transactionSubType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transactionDate' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'transactionId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);

		$this->db->query('create unique index idx_uniqueid on '.$this->tableName.'(uniqueid)');
		$this->db->query('create unique index idx_external_uniqueid on '.$this->tableName.'(external_uniqueid)');
		$this->db->query('create index idx_gameshortcode on '.$this->tableName.'(gameshortcode)');
		$this->db->query('create index idx_player_name on '.$this->tableName.'('.$this->username_col.')');
		$this->db->query('create index idx_game_date on '.$this->tableName.'('.$this->timestamp_col.')');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}