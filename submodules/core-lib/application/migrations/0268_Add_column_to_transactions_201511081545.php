<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_transactions_201511081545 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'total_before_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'total_before_balance');
	}
}