<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_column_player_submit_datetime_of_sale_orders_201711031830 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE sale_orders CHANGE player_submit_datetime player_submit_datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL");
	}

	public function down() {
		//
	}
}