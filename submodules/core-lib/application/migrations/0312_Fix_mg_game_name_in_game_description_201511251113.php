<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201511251113 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//American Roulette
		$this->db->insert($this->tableName, array(
			'game_code' => 'americanroulette', 'english_name' => "American Roulette", 'external_game_id' => "American Roulette",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.americanroulette', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Witch Dr
		$this->db->insert($this->tableName, array(
			'game_code' => 'witchdr', 'english_name' => "Witch Dr", 'external_game_id' => "Witch Dr",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.witchdr', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Jungle 7s
		$this->db->insert($this->tableName, array(
			'game_code' => 'jungle7s', 'english_name' => "Jungle 7s", 'external_game_id' => "Jungle 7s",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.jungle7s', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Break da Bank 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'breakdabank90', 'english_name' => "Break da Bank 90", 'external_game_id' => "Break da Bank 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.breakdabank90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Happy New Year 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'happynewyear90', 'english_name' => "Happy New Year 90", 'external_game_id' => "Happy New Year 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.happynewyear90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Lots a Loot 5 Reel
		$this->db->insert($this->tableName, array(
			'game_code' => 'lotsaloot5reel', 'english_name' => "Lots a Loot 5 Reel", 'external_game_id' => "Lots a Loot 5 Reel",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.lotsaloot5reel', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('americanroulette', 'witchdr', 'jungle7s', 'breakdabank90', 'happynewyear90', 'lotsaloot5reel');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}