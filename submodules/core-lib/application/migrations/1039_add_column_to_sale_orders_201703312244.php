<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sale_orders_201703312244 extends CI_Migration {

	public function up() {
		$fields = array(
			'promo_rules_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'promo_cms_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('sale_orders', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('sale_orders', 'promo_rules_id');
		$this->dbforge->drop_column('sale_orders', 'promo_cms_id');
	}
}
