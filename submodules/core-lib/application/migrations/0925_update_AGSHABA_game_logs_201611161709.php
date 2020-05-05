<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_AGSHABA_game_logs_201611161709 extends CI_Migration {

	public function up() {

		$this->db->trans_start();
		$this->dbforge->drop_column('agshaba_game_logs', 'trans_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'vendor_member_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'operator_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'league_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'sport_type');
		$this->dbforge->drop_column('agshaba_game_logs', 'sync_datetime');
		$this->dbforge->drop_column('agshaba_game_logs', 'match_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'home_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'away_id');
		$this->dbforge->drop_column('agshaba_game_logs', 'match_datetime');
		$this->dbforge->drop_column('agshaba_game_logs', 'bet_type');
		$this->dbforge->drop_column('agshaba_game_logs', 'parlay_ref_no');
		$this->dbforge->drop_column('agshaba_game_logs', 'odds');
		$this->dbforge->drop_column('agshaba_game_logs', 'stake');
		$this->dbforge->drop_column('agshaba_game_logs', 'transaction_time');
		$this->dbforge->drop_column('agshaba_game_logs', 'ticket_status');
		$this->dbforge->drop_column('agshaba_game_logs', 'winlost_amount');
		$this->dbforge->drop_column('agshaba_game_logs', 'winlost');
		$this->dbforge->drop_column('agshaba_game_logs', 'currency');
		$this->dbforge->drop_column('agshaba_game_logs', 'winlost_datetime');
		$this->dbforge->drop_column('agshaba_game_logs', 'odds_type');
		$this->dbforge->drop_column('agshaba_game_logs', 'bet_team');
		$this->dbforge->drop_column('agshaba_game_logs', 'home_hdp');
		$this->dbforge->drop_column('agshaba_game_logs', 'away_hdp');
		$this->dbforge->drop_column('agshaba_game_logs', 'hdp');
		$this->dbforge->drop_column('agshaba_game_logs', 'betfrom');
		$this->dbforge->drop_column('agshaba_game_logs', 'islive');
		$this->dbforge->drop_column('agshaba_game_logs', 'home_score');
		$this->dbforge->drop_column('agshaba_game_logs', 'away_score');
		$this->dbforge->drop_column('agshaba_game_logs', 'custom_info_1');
		$this->dbforge->drop_column('agshaba_game_logs', 'custom_info_2');
		$this->dbforge->drop_column('agshaba_game_logs', 'custom_info_3');
		$this->dbforge->drop_column('agshaba_game_logs', 'custom_info_4');
		$this->dbforge->drop_column('agshaba_game_logs', 'custom_info_5');
		$this->dbforge->drop_column('agshaba_game_logs', 'ba_status');
		$this->dbforge->drop_column('agshaba_game_logs', 'version_key');
		$this->dbforge->drop_column('agshaba_game_logs', 'parlay_detail');
		$this->dbforge->drop_column('agshaba_game_logs', 'error_code');

		$this->dbforge->add_column('agshaba_game_logs', array(
			// 'id' => array(
			// 	'type' => 'INT',
			// 	'unsigned' => TRUE,
			// 	'auto_increment' => TRUE,
			// ),
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'agentcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'netamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'betamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'validbetamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => true,
			),
			'playtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'tablecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'loginip' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'recalcutime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

			'platformtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'remark' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			// 'slottype' => array(
			// 	'type' => 'VARCHAR',
			// 	'constraint' => '32',
			// 	'null' => true,
			// ),
			// 'result' => array(
			// 	'type' => 'TEXT',
			// 	'null' => true,
			// ),
			'mainbillno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			// 'beforecredit' => array(
			// 	'type' => 'DOUBLE',
			// 	'null' => true,
			// ),
			'datatype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '192',
				'null' => false,
			),
             // this was used by AG as gamecode but we should sportstype as gamecode
			'matchid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
		));
		//$this->dbforge->add_key('id', TRUE);

		//$this->dbforge->create_table('agshaba_game_logs');
		$this->db->trans_complete();

	}

	public function down() {
		//$this->dbforge->drop_table('agshaba_game_logs');
	}
}
