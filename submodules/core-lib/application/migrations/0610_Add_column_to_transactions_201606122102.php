<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_transactions_201606122102 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'ip_used' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'ip_used');
	}
}
