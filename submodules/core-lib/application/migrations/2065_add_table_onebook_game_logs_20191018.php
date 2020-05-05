<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_onebook_game_logs_20191018 extends CI_Migration {

	private $tableName = 'onebook_thb1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'vendor_member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'operator_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'match_id' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'team_id' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'home_id' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'away_id' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'match_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'sport_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'parlay_ref_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'odds' => array(
				'type' => 'double',
                'null' => true,
			),
			'original_stake' => array(
				'type' => 'double',
                'null' => true,
			),
			'stake' => array(
				'type' => 'double',
                'null' => true,
			),
			'range' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'validbetamount' => array(
				'type' => 'double',
                'null' => true,
			),
			'transaction_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'ticket_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'commission' => array(
				'type' => 'double',
                'null' => true,
			),
			'buyback_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'winlost_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'after_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'winlost_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'odds_type' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'odds_info' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'lottery_bettype' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'bet_team' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'exculding' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'islucky' => array(
				'type' => 'VARCHAR',
				'constraint' => '5',
				'null' => true,
			),
			'parlay_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'combo_type' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'bet_tag' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'pool_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betchoice' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'home_hdp' => array(
				'type' => 'double',
                'null' => true,
			),
			'away_hdp' => array(
				'type' => 'double',
                'null' => true,
			),
			'hdp' => array(
				'type' => 'double',
                'null' => true,
			),
			'betfrom' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'islive' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'last_ball_no' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'home_score' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'away_score' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'os' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'browser' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'settlement_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'custominfo1' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'custominfo2' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'custominfo3' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'custominfo4' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'custominfo5' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'ba_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '1',
				'null' => true,
			),
			'bonus_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'wallet_type' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'ref_code' => array(
				'type' => 'INT',
				'constraint' => '64',
                'null' => true,
			),
			'version_key' => array(
				'type' => 'INT',
				'constraint' => '64',
                'null' => true,
			),
			'parlaydata' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'colossusbetdata' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'cashoutdata' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'version_key' => array(
				'type' => 'INT',
				'constraint' => '32',
                'null' => true,
			),
			'race_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'race_lane' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'last_version_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'last_sync_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			# SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_vendor_member_id', 'vendor_member_id');
	        $this->player_model->addIndex($this->tableName, 'idx_sport_type', 'sport_type');
	        $this->player_model->addIndex($this->tableName, 'idx_transaction_time', 'transaction_time');
	        $this->player_model->addIndex($this->tableName, 'idx_settlement_time', 'settlement_time');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_trans_id', 'trans_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
