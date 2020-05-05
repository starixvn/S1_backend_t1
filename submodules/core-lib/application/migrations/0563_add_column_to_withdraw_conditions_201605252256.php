<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdraw_conditions_201605252256 extends CI_Migration {

	public function up() {
		$fields = array(
			'deposit_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'bonus_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'bet_times' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('withdraw_conditions', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('withdraw_conditions', 'deposit_amount');
		$this->dbforge->drop_column('withdraw_conditions', 'bonus_amount');
		$this->dbforge->drop_column('withdraw_conditions', 'bet_times');
		$this->dbforge->drop_column('withdraw_conditions', 'bet_amount');
	}
}