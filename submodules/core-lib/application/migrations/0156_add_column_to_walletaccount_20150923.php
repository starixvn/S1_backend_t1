<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_walletaccount_20150923 extends CI_Migration {

	private $tableName = 'walletaccount';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'is_checking' => array(
				'type' => 'VARCHAR',
				'constraint' => 6,
				'DEFAULT' => false,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'is_checking');
	}
}