<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ipm_v2_gamelogs_201709151540 extends CI_Migration {

	private $tableName = 'ipm_v2_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'Provider' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'WagerCreationDateTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'PlayerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'StakeAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'MemberExposure' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'PayoutAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'WinLoss' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'OddsType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'WagerType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'isSettled' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'isConfirmed' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'isCancelled' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetTradeStatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetTradeCommission' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'BetTradeBuybackAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ComboType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'LastUpdatedDate' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'DetailItems' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
