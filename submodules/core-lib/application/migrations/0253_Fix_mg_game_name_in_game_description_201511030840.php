<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201511030840 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//Double Double Bonus
		$this->db->insert($this->tableName, array(
			'game_code' => 'doubledoublebonus', 'english_name' => "Double Double Bonus", 'external_game_id' => "Double Double Bonus",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.doubledoublebonus', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('doubledoublebonus');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}