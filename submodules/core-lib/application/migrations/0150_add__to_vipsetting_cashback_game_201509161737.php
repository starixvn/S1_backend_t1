<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add__to_vipsetting_cashback_game_201509161737 extends CI_Migration {

	private $tableName = 'vipsetting_cashback_game';

	public function up() {

		//add total betting amount
		$this->dbforge->add_column($this->tableName, array(
			"percentage" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			"maxBonus" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'percentage');
		$this->dbforge->drop_column($this->tableName, 'maxBonus');
	}
}