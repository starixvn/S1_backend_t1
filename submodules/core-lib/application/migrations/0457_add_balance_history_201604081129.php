<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_balance_history_201604081129 extends CI_Migration {

	private $tableName = 'balance_history';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'aff_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'user_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'record_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'action_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'main_wallet' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'sub_wallet' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'transaction_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'playerpromo_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'sale_order_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'walletaccount_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'sub_wallet_id' => array(
				'type' => 'INT',
				'null' => true,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->dbforge->add_column('affiliates', array(
			'wallet_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			))
		);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
		$this->dbforge->drop_column('affiliates', 'wallet_balance');
	}
}

///END OF FILE//////////