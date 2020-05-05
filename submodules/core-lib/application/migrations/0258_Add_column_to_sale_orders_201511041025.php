<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_sale_orders_201511041025 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'processed_checking_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'processed_approved_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'processed_checking_time');
		$this->dbforge->drop_column($this->tableName, 'processed_approved_time');
	}
}