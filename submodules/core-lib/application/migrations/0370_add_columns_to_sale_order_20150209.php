<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_sale_order_20150209 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {
		
		$this->dbforge->add_column($this->tableName, array(
			'sub_wallet_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'group_level_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		$this->dbforge->add_column('vipsetting', array(
			'can_be_self_join_in' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'sub_wallet_id');
		$this->dbforge->drop_column($this->tableName, 'group_level_id');
		$this->dbforge->drop_column('vipsetting', 'can_be_self_join_in');
	}
}

///END OF FILE//////////