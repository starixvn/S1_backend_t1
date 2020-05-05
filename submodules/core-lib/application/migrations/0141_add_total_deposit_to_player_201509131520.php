<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_total_deposit_to_player_201509131520 extends CI_Migration {

	private $tableName = 'player';

	public function up() {

		//add total betting amount
		$this->dbforge->add_column($this->tableName, array(
			"totalDepositAmount" => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'totalDepositAmount');
	}
}