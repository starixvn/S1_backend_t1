<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sales_order_201607260209 extends CI_Migration {
	private $tableName = 'sale_orders';
	public function up() {
		$fields = array(
			'pending_deposit_wallet_type' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'pending_deposit_wallet_type');
	}
}
