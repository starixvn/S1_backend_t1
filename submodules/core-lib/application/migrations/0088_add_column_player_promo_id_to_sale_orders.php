<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_player_promo_id_to_sale_orders extends CI_Migration {

	public function up() {
		$fields = array(
			'player_promo_id' => array(
				'type' => 'INT',
				'unsigned' => false,
				'null' => true,
			),
		);
		$this->dbforge->add_column('sale_orders', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('sale_orders', 'player_promo_id');
	}
}