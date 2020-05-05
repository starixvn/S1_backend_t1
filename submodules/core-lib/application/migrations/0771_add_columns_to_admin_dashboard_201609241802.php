<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_admin_dashboard_201609241802 extends CI_Migration {

	private $tableName = 'admin_dashboard';

	public function up() {
		$fields = array(
			'total_all_balance_include_subwallet' => array(
				'type' => 'INT',
				'null' => true,
				'constrain'=>11
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'total_all_balance_include_subwallet');
	}
}
