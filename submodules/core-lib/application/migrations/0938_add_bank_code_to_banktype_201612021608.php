<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_bank_code_to_banktype_201612021608 extends CI_Migration {

	public function up() {

		$this->dbforge->add_column('banktype', array(
			'bank_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'extra_info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));

		//update bank code
		$this->load->model(['banktype']);
		$this->banktype->updateBankCode();

		$this->db->query('create unique index idx_bank_code on banktype(bank_code) ');
	}

	public function down() {

		$this->dbforge->drop_column('banktype', 'bank_code');
		$this->dbforge->drop_column('banktype', 'extra_info');

	}
}
