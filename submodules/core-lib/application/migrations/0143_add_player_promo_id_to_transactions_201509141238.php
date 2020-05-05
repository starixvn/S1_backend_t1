<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_player_promo_id_to_transactions_201509141238 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {

		//add total betting amount
		$this->dbforge->add_column($this->tableName, array(
			"player_promo_id" => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'player_promo_id');
	}
}