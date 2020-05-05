<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_flag_to_transactions extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'flag' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'flag');
	}
}
