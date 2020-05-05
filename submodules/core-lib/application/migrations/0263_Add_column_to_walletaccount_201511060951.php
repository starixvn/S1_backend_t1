<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_walletaccount_201511060951 extends CI_Migration {

	private $tableName = 'walletaccount';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'processed_checking_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'processed_checking_time');
	}
}