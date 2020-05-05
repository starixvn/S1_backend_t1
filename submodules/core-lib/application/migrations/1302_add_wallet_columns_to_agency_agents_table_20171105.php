<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_wallet_columns_to_agency_agents_table_20171105 extends CI_Migration {

	private $tableName = 'agency_agents';

	public function up() {
        $fields = array(
			'wallet_balance' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'wallet_hold' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'frozen' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'wallet_balance');
		$this->dbforge->drop_column($this->tableName, 'wallet_hold');
		$this->dbforge->drop_column($this->tableName, 'frozen');
	}
}
