<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebet_game_logs_20160704 extends CI_Migration {

	private $tableName = 'ebet_game_logs';
	private $username_col = 'username';
	private $timestamp_col = 'payoutTime';

# 'gameType'
# 'betMap'
# 'judgeResult'
# 'roundNo'
# 'payout'
# 'bankerCards'
# 'playerCards'
# 'allDices'
# 'dragonCard'
# 'tigerCard'
# 'number'
# 'createTime'
# 'payoutTime'
# 'betHistoryId'
# 'validBet'

# 'userId'
# 'username'
# 'subChannelId'

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'gameType' => array(
				'type' => 'INT',
				'null' => true,
			),
			'betMap' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'judgeResult' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'roundNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bankerCards' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'playerCards' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'allDices' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
			'dragonCard' => array(
				'type' => 'INT',
				'null' => true,
			),
			'tigerCard' => array(
				'type' => 'INT',
				'null' => true,
			),
			'number' => array(
				'type' => 'INT',
				'null' => true,
			),
			'createTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'payoutTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'betHistoryId' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'validBet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'userId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'subChannelId' => array(
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