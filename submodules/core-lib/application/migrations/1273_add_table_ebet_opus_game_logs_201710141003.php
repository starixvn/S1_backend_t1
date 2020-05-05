<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebet_opus_game_logs_201710141003 extends CI_Migration {

	private $tableName = 'ebet_opus_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ebet_opus_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'third_party' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tag' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_category' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'match_datetime' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'parlay_ref_no' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'odds' => array(
				'type' => 'DOUBLE',
                'null' => true,                
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'stake' => array(
				'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
			),
			'winlost_amount' => array(
				'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
			),
			'transaction_time' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ticket_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'version_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds_type' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'sports_type' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'bet_team' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'home_hdp' => array(
				'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
			),
			'away_hdp' => array(
				'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
			),
			'match_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'is_live' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'home_score' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'away_score' => array(
				'type' => 'INT',				
				'null' => true,
			),
			'choice_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'choice_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'txn_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'last_update' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'home_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'away_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sport_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winlost_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),

			  // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
		);

		$this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        $this->db->query('create unique index idx_external_uniqueid on ebet_opus_game_logs(external_uniqueid)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
		$this->db->query('drop index idx_external_uniqueid on ebet_opus_game_logs');
	}
}
