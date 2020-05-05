<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_sale_orders_111216141635 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE sale_orders ADD player_submit_datetime DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
	}

	public function down() {
		$this->dbforge->drop_column('sale_orders', 'player_submit_datetime');
	}
}