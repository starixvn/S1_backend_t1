<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_transaction_id_to_sale_orders_201509120655 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {
		$fields = array(
			'transaction_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		//add order_id to transaction
		$fields = array(
			'order_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_column('transactions', $fields);

		$this->db->query('create unique index idx_operator_settings_name on operator_settings(name)');

		$this->load->model(array('player_model'));

		//add total betting amount
		$this->dbforge->add_column('player', array(
			"totalBettingAmount" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"refereePlayerId" => array(
				'type' => 'INT',
				'null' => true,
			),
			"refereeEnabledStatus" => array(
				'type' => 'INT',
				'default' => Player_model::STATUS_DISABLED,
				'null' => true,
			),
			"refereeEnabledDatetime" => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->db->query('drop index idx_operator_settings_name on operator_settings');
		$this->dbforge->drop_column($this->tableName, 'transaction_id');
		$this->dbforge->drop_column('transactions', 'order_id');
		$this->dbforge->drop_column('transactions', 'payment_account_id');
		$this->dbforge->drop_column('transactions', 'updated_at');
		$this->dbforge->drop_column('player', 'totalBettingAmount');
		$this->dbforge->drop_column('player', 'refereePlayerId');
		$this->dbforge->drop_column('player', 'refereeEnabledStatus');
		$this->dbforge->drop_column('player', 'refereeEnabledDatetime');
	}
}