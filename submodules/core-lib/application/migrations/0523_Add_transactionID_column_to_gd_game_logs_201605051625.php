<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_transactionID_column_to_gd_game_logs_201605051625 extends CI_Migration {

	public function up() {
		$gd_game_logs_fields = array(
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			);
	
		$this->dbforge->add_column('gd_game_logs', $gd_game_logs_fields);
	}

	public function down() {
		$this->dbforge->drop_column('gd_game_logs', 'transaction_id');
    }
}