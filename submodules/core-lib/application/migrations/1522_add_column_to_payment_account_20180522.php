<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_payment_account_20180522 extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {

		$fields = array(
			'second_category_flag' => array(
                'type' => 'INT',
                'null' => false,
				'default' => 1,
			),

		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'second_category_flag');
	}
}
