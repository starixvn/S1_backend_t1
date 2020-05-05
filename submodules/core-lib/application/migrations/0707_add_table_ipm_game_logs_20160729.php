<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ipm_game_logs_20160729 extends CI_Migration {

	private $tableName = 'ipm_game_logs';
	private $username_col = 'memberCode';
	private $timestamp_col = 'betTime';

// betTime
// memberCode
// matchDateTime
// sportsName
// matchID
// leagueName
// homeTeam
// awayTeam
// favouriteTeamFlag
// betType
// selection
// handicap
// oddsType
// odds
// currency
// betAmt
// result
// HTHomeScore
// HTAwayScore
// FTHomeScore
// FTAwayScore
// BetHomeScore
// BetAwayScore
// settled
// betCancelled
// bettingMethod
// BTStatus
// BTComission

// ParlaySign
// ParlayBetType
// ParlayBetOn
// ParlayHandicap
// ParlayOdds
// ParlayFavoriteTeamFlag
// ParlayLeagueName
// ParlayBetCancelled
// ParlayTeamAway
// ParlayTeamHome
// ParlayBetTime
// ParlayMatchDateTime
// ParlaySportName
// ParlayHTHomeScore
// ParlayHTAwayScore
// ParlayFTHomeScore
// ParlayFTAwayScore
// ParlayBetHomeScore
// ParlayBetAwayScore
// statusCode
// statusDesc

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'betId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'betTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'memberCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'matchDateTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'sportsName' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'matchID' => array(
				'type' => 'INT',
				'null' => true,
			),
			'leagueName' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'homeTeam' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'awayTeam' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'favouriteTeamFlag' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'betType' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'selection' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'handicap' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'oddsType' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'betAmt' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'HTHomeScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'HTAwayScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'FTHomeScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'FTAwayScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'BetHomeScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'BetAwayScore' => array(
				'type' => 'INT',
				'null' => true,
			),
			'settled' => array(
				'type' => 'INT',
				'null' => true,
			),
			'betCancelled' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bettingMethod' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'BTStatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'BTComission' => array(
				'type' => 'DOUBLE',
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