<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_before_after_balance_to_walletaccount_201510201606 extends CI_Migration {

	private $tableName = 'walletaccount';

	public function up() {
		//modify column
		$fields = array(
			'before_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		//add column
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'before_balance');
		$this->dbforge->drop_column($this->tableName, 'after_balance');
	}
}

////END OF FILE//////////