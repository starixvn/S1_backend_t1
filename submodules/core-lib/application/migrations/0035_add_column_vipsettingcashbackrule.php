<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_vipsettingcashbackrule extends CI_Migration {

	public function up() {
		$fields = array(
			'downgradeAmount' => array(
				'type' => 'INT',
				'null' => true,
			),
			'upgradeAmount' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('vipsettingcashbackrule', $fields, 'dailyMaxWithdrawal');
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'upgradeAmount');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'downgradeAmount');
	}
}