<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_account_to_external_system_201509132123 extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		//add total betting amount
		$this->dbforge->add_column($this->tableName, array(
			"sandbox_account" => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			"live_account" => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

		//update sandbox of gopay
		$data = array(
			'sandbox_url' => 'https://mertest.gopay.com.cn/PGServer/Trans/WebClientAction.do',
			'sandbox_key' => '0000003358',
			'sandbox_account' => '0000000001000000584',
			'sandbox_secret' => '12345678',
			'live_mode' => '0',
		);
		$this->db->where('id', GOPAY_PAYMENT_API);
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'sandbox_account');
		$this->dbforge->drop_column($this->tableName, 'live_account');
	}
}