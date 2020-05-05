<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_sale_orders_201512171305 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_sale_order_trans_id on sale_orders(transaction_id)');
	}

	public function down() {
		$this->db->query('drop index idx_sale_order_trans_id on sale_orders');
	}
}

///END OF FILE//////////