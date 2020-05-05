<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_banktype_20160112 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'enabled_withdrawal' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'enabled_deposit' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'enabled_withdrawal');
		$this->dbforge->drop_column($this->tableName, 'enabled_deposit');
	}
}