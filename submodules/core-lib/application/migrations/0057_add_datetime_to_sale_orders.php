<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_datetime_to_sale_orders extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		//1 = game api, 2= payment
		$this->dbforge->add_column($this->tableName, array(
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'created_at');
		$this->dbforge->drop_column($this->tableName, 'updated_at');
	}
}
