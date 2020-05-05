<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_201606260100 extends CI_Migration {

	public function up() {
		$fields = array(
			'transaction_fee' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('walletaccount', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'transaction_fee');
	}
}