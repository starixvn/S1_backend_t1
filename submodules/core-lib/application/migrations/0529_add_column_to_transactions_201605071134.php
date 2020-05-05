<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transactions_201605071134 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'trans_date' => array(
				'type' => 'DATE',
				'null' => true,
			),
			'trans_year_month' => array(
				'type' => 'VARCHAR',
				'constraint' => '6',
				'null' => true,
			),
			'trans_year' => array(
				'type' => 'VARCHAR',
				'constraint' => '4',
				'null' => true,
			),
		]);

		$this->db->query('create index idx_trans_date on transactions(trans_date)');
		$this->db->query('create index idx_trans_year_month on transactions(trans_year_month)');
		$this->db->query('create index idx_trans_year on transactions(trans_year)');
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'trans_date');
		$this->dbforge->drop_column($this->tableName, 'trans_year_month');
		$this->dbforge->drop_column($this->tableName, 'trans_year');
	}
}