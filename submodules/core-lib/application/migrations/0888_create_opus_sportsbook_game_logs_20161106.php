<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_opus_sportsbook_game_logs_20161106 extends CI_Migration {

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 13,
			),
			'member_id' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 20,
			),
			'operator_id' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 50,
			),
			'site_code' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 20,
			),
			'league_id' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'match_datetime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'parlay_ref_no' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 3,
			),
			'stake' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'winlost_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'transaction_time' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'ticket_status' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 10,
			),
			'version_key' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'winlost_datetime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'odds_type' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'sports_type' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 2,
			),
			'bet_team' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'home_hdp' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'away_hdp' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'is_live' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 1,
			),
			'home_score' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'away_score' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'choicecode' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'choicename' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'txn_type' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'last_update' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'leaguename' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'homename' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'awayname' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'sportname' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'oddsname' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'bettypename' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 255,
			),
			'winlost_status' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 1,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 100,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 100,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'null' => true,
				'constraint' => 100,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('transaction_time');
		$this->dbforge->add_key('uniqueid');
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->add_key('response_result_id');
		$this->dbforge->create_table('opus_sportsbook_game_logs');
	}

	public function down() {
		$this->dbforge->drop_table('opus_sportsbook_game_logs');
	}
}

