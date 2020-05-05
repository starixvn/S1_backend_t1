<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gsag_game_logs_20160422 extends CI_Migration {

	public function up() {
		$fields = array(
			'betAmountBase' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'betAmountBonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'mainbillno' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'netAmountBase' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'netAmountBonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'slottype' => array(
				'type' => 'INTEGER',
				'null' => true,
			),
		);

		$this->dbforge->add_column('gsag_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('gsag_game_logs', array('betAmountBase','betAmountBonus','mainbillno','netAmountBase','netAmountBonus','slottype'));
	}
}