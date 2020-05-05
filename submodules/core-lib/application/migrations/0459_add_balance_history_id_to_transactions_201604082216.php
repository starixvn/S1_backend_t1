<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_balance_history_id_to_transactions_201604082216 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'balance_history_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'balance_history_id');
	}
}

///END OF FILE//////////