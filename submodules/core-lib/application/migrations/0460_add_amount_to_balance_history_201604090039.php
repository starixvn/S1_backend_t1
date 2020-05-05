<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_amount_to_balance_history_201604090039 extends CI_Migration {

	public function up() {

		$this->dbforge->add_column('balance_history', array(
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => true,
			),

		));

		$this->dbforge->add_column('transactions', array(
			'changed_balance' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		));

	}

	public function down() {
		$this->dbforge->drop_column('balance_history', 'amount');
		$this->dbforge->drop_column('balance_history', 'game_platform_id');

		$this->dbforge->drop_column('transactions', 'changed_balance');
	}
}

///END OF FILE//////////