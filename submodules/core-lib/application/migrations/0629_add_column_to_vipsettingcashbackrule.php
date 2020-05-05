<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_vipsettingcashbackrule extends CI_Migration {

	public function up() {
		$fields = array(
			'winning_convert_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'losing_convert_rate' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'minimumMonthlyDeposit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('vipsettingcashbackrule', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'winning_convert_rate');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'losing_convert_rate');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'minimumMonthlyDeposit');
	}
}