<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agency_daily_player_settlement_201706231500 extends CI_Migration {

	private $tableName = 'agency_daily_player_settlement';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
			),
			'agent_id' => array(
				'type' => 'INT',
			),
			'settlement_period' => array(
                'type' => 'VARCHAR',
                'constraint' => '36',
			),
			'settlement_date_from' => array(
				'type' => 'DATETIME',
			),
			'settlement_date' => array(
				'type' => 'DATETIME',
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
			),
			'rolling_comm_basis' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
			),
			'real_bets' => array(
				'type' => 'DOUBLE',
			),
			'bets' => array(
				'type' => 'DOUBLE',
			),
			'tie_bets' => array(
				'type' => 'DOUBLE',
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
			),
			'lost_bets' => array(
				'type' => 'DOUBLE',
			),
			'bets_except_tie' => array(
				'type' => 'DOUBLE',
			),
			'player_commission' => array(
				'type' => 'DOUBLE',
			),
			'roll_comm_income' => array(
				'type' => 'DOUBLE',
			),
			'agent_commission' => array(
				'type' => 'DOUBLE',
			),
			'wins' => array(
				'type' => 'DOUBLE',
			),
			'bonuses' => array(
				'type' => 'DOUBLE',
			),
			'rebates' => array(
				'type' => 'DOUBLE',
			),
			'net_gaming' => array(
				'type' => 'DOUBLE',
			),
			'rev_share_amt' => array(
				'type' => 'DOUBLE',
			),
			'earnings' => array(
				'type' => 'DOUBLE',
			),
			'created_on' => array(
				'type' => 'DATETIME',
			),
			'updated_on' => array(
				'type' => 'DATETIME',
			),
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `agency_daily_player_settlement` ADD UNIQUE INDEX `agency_daily_player_settlement_idx` (`player_id` ASC, `agent_id` ASC, `settlement_period` ASC, `settlement_date_from` ASC)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
