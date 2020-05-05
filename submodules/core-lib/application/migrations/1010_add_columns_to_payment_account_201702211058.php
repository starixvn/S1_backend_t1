<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_payment_account_201702211058 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {
		$fields = array(
			'deleted_at' => array(
				'type' => 'datetime',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'deleted_at');
	}
}
