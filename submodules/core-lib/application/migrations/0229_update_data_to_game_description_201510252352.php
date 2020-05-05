<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_data_to_game_description_201510252352 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->db->query('update game_description set no_cash_back=0, void_bet=0');

	}

	public function down() {
	}
}