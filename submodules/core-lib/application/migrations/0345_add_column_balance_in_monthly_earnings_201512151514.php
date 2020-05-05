<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_balance_in_monthly_earnings_201512151514 extends CI_Migration {

	public function up() {
		$fields = array(
			'balance' => array(
				'type' => 'double',
				'null' => true,
			),
		);
		$this->dbforge->add_column('monthly_earnings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('monthly_earnings', $this->up->fields);
	}
	
}