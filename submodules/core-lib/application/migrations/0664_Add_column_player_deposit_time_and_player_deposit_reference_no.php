<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_player_deposit_time_and_player_deposit_reference_no extends CI_Migration {

	public function up() {
		$fields = array(
			'player_deposit_reference_no' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
			'player_deposit_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
		);
		
		$this->dbforge->add_column('sale_orders', $fields, 'player_deposit_slip_path');
	}

	public function down() {
		$this->dbforge->drop_column('sale_orders', 'player_deposit_time');
		$this->dbforge->drop_column('sale_orders', 'player_deposit_reference_no');
	}



}