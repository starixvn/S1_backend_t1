<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_secure_id_to_sale_orders_201510141341 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'secure_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
		//unique
		$this->db->query("create unique index idx_secure_id on sale_orders(secure_id)");
	}

	public function down() {
		$this->db->query("drop index idx_secure_id on sale_orders");
		$this->dbforge->drop_column($this->tableName, 'secure_id');
	}
}
