<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_201704082319 extends CI_Migration {

	public function up() {
		$fields = array(
			'player_bank_details_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('walletaccount', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('walletaccount', 'player_bank_details_id');
	}
}
