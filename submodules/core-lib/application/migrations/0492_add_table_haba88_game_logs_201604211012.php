<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_haba88_game_logs_201604211012 extends CI_Migration {
	private $tableName = 'haba88_game_logs';
	public function up() {
		$this->dbforge->drop_table($this->tableName);

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'auto_increment' => TRUE,
				'null' => false
			),
			'PlayerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'BrandGameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'GameKeyName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'GameTypeId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'DtStarted' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'DtCompleted' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'FriendlyGameInstanceId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'GameInstanceId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'Stake' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'Payout' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'JackpotWin' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'JackpotContribution' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'CurrencyCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'ChannelTypeId' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true
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
		$this->db->query('create index idx_haba88_game_logs_external_uniqueid on haba88_game_logs(external_uniqueid)');
	}

	public function down() {
		$this->db->query('drop index idx_haba88_game_logs_external_uniqueid on haba88_game_logs');
		$this->dbforge->drop_table($this->tableName);
	}
}