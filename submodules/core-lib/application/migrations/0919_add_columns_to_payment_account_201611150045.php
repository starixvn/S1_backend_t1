<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_payment_account_201611150045 extends CI_Migration {

	private $tableName = 'payment_account';
	public function up() {
		$fields = array(
			'max_deposit_trans' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'min_deposit_trans' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
        $this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'min_deposit_trans');
        $this->dbforge->drop_column($this->tableName, 'max_deposit_trans');
	}
}
