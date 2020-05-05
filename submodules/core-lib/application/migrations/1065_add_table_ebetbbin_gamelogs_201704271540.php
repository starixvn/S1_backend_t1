<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebetbbin_gamelogs_201704271540 extends CI_Migration {

	private $tableName = 'ebetbbin_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'ebetbbinID' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'tag' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'thirdParty' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Origin' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'RoundNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ExchangeRate' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ResultType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'WagersID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Result' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Card' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Commissionable' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Payoff' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'WagersDate' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'WagerDetail' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'SerialID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'IsPaid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
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
		$this->load->model(['player_model']);
		$this->player_model->addIndex($this->tableName, 'idx_WagersDate', 'WagersDate');
		$this->player_model->addIndex($this->tableName, 'idx_type', 'type');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
