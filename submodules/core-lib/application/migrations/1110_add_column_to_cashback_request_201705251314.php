<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_cashback_request_201705251314 extends CI_Migration {

	private $tableName = 'cashback_request';

	public function up() {
		$fields = array(
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'withdraw_condition_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet_amount');
		$this->dbforge->drop_column($this->tableName, 'withdraw_condition_amount');
	}
}