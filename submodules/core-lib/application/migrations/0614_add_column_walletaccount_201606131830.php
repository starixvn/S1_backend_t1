<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_walletaccount_201606131830 extends CI_Migration {
	private $tableName = 'walletaccount';
	public function up() {
		$fields = array(
			'transaction_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'transaction_id');
	}
}