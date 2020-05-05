<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_201607080005 extends CI_Migration {
	private $tableName = 'walletaccount';
	public function up() {
		$fields = array(
			'paymentAPI' => array(
				'type' => 'int',
				'null' => false,
				'default' => 0
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'paymentAPI');
	}
}
