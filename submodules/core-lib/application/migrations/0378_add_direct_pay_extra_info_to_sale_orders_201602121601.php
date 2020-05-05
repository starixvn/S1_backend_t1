<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_direct_pay_extra_info_to_sale_orders_201602121601 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'direct_pay_extra_info' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'direct_pay_extra_info');
	}
}

///END OF FILE//////////