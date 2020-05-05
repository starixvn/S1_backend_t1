<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agshaba_game_logs_2016100217087 extends CI_Migration {

	private $tableName = 'agshaba_game_logs';

	public function up() {

		$this->db->trans_start();
		// Drop the existing
		$this->dbforge->drop_table($this->tableName);

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
				),
			'trans_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'vendor_member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'operator_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'league_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'match_id' => array(
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
				'type' => 'DATETIME',
				'null' => true,
				),
			'sport_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'parlay_ref_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'stake' => array(
				'type' => 'DOUBLE',
				'null' => true,
				),
			'transaction_time' => array(
				'type' => 'DATETIME',
				'null' => true,
				),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'ticket_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'winlost_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				),

			'winlost' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'after_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'winlost_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
				),
			'odds_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'bet_team' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'home_hdp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'away_hdp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'hdp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'betfrom' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'islive' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'home_score' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'away_score' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'custom_info_1' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'custom_info_2' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'custom_info_3' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'custom_info_4' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'custom_info_5' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'ba_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'version_key' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'parlay_detail' => array(
				'type' => 'TEXT',
				'null' => true,
				),
			'error_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
				),
			'sync_datetime' => array(
				'type' => 'TIMESTAMP',
				'null' => false,
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

$this->db->trans_complete();
}

public function down() {
	$this->dbforge->drop_table($this->tableName);
}
}