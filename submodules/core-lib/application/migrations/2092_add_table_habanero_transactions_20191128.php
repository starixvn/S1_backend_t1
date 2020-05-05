<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_habanero_transactions_20191128 extends CI_Migration {

	private $tableName = 'habanero_transactions';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'dtsent' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'brandgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'keyname' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'auth_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'auth_passkey' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'auth_machinename' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'auth_locale' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'auth_brandid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'accountid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'customplayertype' => array(
                "type" => "TINYINT",
                "null" => true
			),
			'gameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'friendlygameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'isretry' => array(
                "type" => "TINYINT",
                "null" => true		
			),
			'retrycount' => array(
                "type" => "TINYINT",
                "null" => true
			),
			'isrefund' => array(
                "type" => "TINYINT",
                "null" => true	
			),
			'isrecredit' => array(
                "type" => "TINYINT",
                "null" => true
			),
            "funds_debitandcredit" => [
                "type" => "TINYINT",
                "null" => true
			],
			'fundinfo_gamestatemode' => array(
                "type" => "TINYINT",
                "null" => true
			),
			'fundinfo_transferid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'fundinfo_currencycode' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'fundinfo_amount' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'fundinfo_jpwin' => array(
                "type" => "TINYINT",
                "null" => true	
			),
			'fundinfo_jpcont' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'null' => true,
			),
			'fundinfo_isbonus' => array(
				"type" => "TINYINT",
                "null" => true
			),
			'fundinfo_dtevent' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'fundinfo_initialdebittransferid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_keyname' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamedetails_gametypeid' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'gamedetails_gametypename' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamedetails_brandgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_gamesessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_gameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_friendlygameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamedetails_channel' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'gamedetails_device' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamedetails_browser' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),

			'bonusdetails_bonusbalanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bonusdetails_couponid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bonusdetails_coupontypeid' => array(
                "type" => "TINYINT",
                "null" => true
			),
			'bonusdetails_couponcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),




			'raw_data' => array(
                'type' => 'TEXT',
                'null' => true,
			),
			'is_valid_transaction' => array(
                "type" => "TINYINT",
				"null" => true,
				'default' => 0,
			),
			'player_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
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
		);

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_fundinfo_transferid', 'fundinfo_transferid');
	        $this->player_model->addIndex($this->tableName, 'idx_gamedetails_gametypeid', 'gamedetails_gametypeid');
	        $this->player_model->addIndex($this->tableName, 'idx_gamedetails_gamesessionid', 'gamedetails_gamesessionid');
	        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');	        
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
