<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_ignore_add_agency_wl_settlement_201706291230 extends CI_Migration {

	// private $tableName = 'agency_wl_settlement';

	public function up() {
		// $fields = array(
		// 	'id' => array(
		// 		'type' => 'INT',
		// 		'null' => false,
		// 		'auto_increment' => TRUE,
		// 	),
  //           'status' => array(
  //               'type' => 'VARCHAR',
  //               'constraint' => '45',
  //           ),
  //           'type' => array(
  //               'type' => 'VARCHAR',
  //               'constraint' => '20',
  //           ),
		// 	'user_id' => array(
		// 		'type' => 'INT',
  //               'null' => true,
		// 	),
		// 	'settlement_date_from' => array(
		// 		'type' => 'DATETIME',
		// 	),
		// 	'settlement_date_to' => array(
		// 		'type' => 'DATETIME',
		// 	),
		// 	'rev_share' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'rolling_comm' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'rolling_comm_basis' => array(
		// 		'type' => 'VARCHAR',
		// 		'constraint' => '36',
		// 	),
		// 	'real_bets' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'bets' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'tie_bets' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'result_amount' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'lost_bets' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'bets_except_tie' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'player_commission' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'roll_comm_income' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'agent_commission' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'wins' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'bonuses' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'rebates' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'net_gaming' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'rev_share_amt' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'earnings' => array(
		// 		'type' => 'DOUBLE',
		// 	),
		// 	'created_on' => array(
		// 		'type' => 'DATETIME',
		// 	),
		// 	'updated_on' => array(
		// 		'type' => 'DATETIME',
		// 	),
		// );

		// $this->dbforge->add_field($fields);
		// $this->dbforge->add_key('id', TRUE);

		// $this->dbforge->create_table($this->tableName);

  //       $this->db->query('ALTER TABLE `agency_wl_settlement` ADD UNIQUE INDEX `agency_wl_settlement_idx` (`type`, `user_id`, `settlement_date_from`, `settlement_date_to`)');
	}

	public function down() {
		// $this->dbforge->drop_table($this->tableName);
	}
}
