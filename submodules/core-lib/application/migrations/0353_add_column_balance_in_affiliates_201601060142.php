<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_balance_in_affiliates_201601060142 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('affiliates', array(
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('affiliates', 'balance');
	}
}

///END OF FILE//////////