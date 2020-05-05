<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_transactions_201606131829 extends CI_Migration {
	private $tableName = 'transactions';
	public function up() {
		$fields = array(
			'related_trans_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'related_trans_id');
	}
}