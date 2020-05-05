<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201511172130 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//Dolphin Tale 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'dolphintale90', 'english_name' => "Dolphin Tale 90", 'external_game_id' => "Dolphin Tale 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.dolphintale90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Adventure Palace (Adventure Palace)
		$this->db->insert($this->tableName, array(
			'game_code' => 'adventurepalace_adventurepalace', 'english_name' => "Adventure Palace (Adventure Palace)", 'external_game_id' => "Adventure Palace (Adventure Palace)",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.adventurepalace_adventurepalace', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Tomb Raiderecret of theword 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'tombraiderecretoftheword90', 'english_name' => "Tomb Raiderecret of theword 90", 'external_game_id' => "Tomb Raiderecret of theword 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tombraiderecretoftheword90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Cashville 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'cashville90', 'english_name' => "Cashville 90", 'external_game_id' => "Cashville 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cashville90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Jellyfish Jaunt 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'jellyfishjaunt90', 'english_name' => "Jellyfish Jaunt 90", 'external_game_id' => "Jellyfish Jaunt 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.jellyfishjaunt90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//So Many Monsters
		$this->db->insert($this->tableName, array(
			'game_code' => 'somanymonsters', 'english_name' => "So Many Monsters", 'external_game_id' => "So Many Monsters",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.somanymonsters', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Basketball Star
		$this->db->insert($this->tableName, array(
			'game_code' => 'basketballstar', 'english_name' => "Basketball Star", 'external_game_id' => "Basketball Star",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.basketballstar', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//So Much Sushi
		$this->db->insert($this->tableName, array(
			'game_code' => 'somuchsushi', 'english_name' => "So Much Sushi", 'external_game_id' => "So Much Sushi",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.somuchsushi', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//So Much Candy
		$this->db->insert($this->tableName, array(
			'game_code' => 'somuchcandy', 'english_name' => "So Much Candy", 'external_game_id' => "So Much Candy",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.somuchcandy', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Big Break 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'bigbreak90', 'english_name' => "Big Break 90", 'external_game_id' => "Big Break 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.bigbreak90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('dolphintale90', 'adventurepalace_adventurepalace',
			'tombraiderecretoftheword90', 'cashville90', 'jellyfishjaunt90', 'somanymonsters',
			'basketballstar', 'somuchsushi', 'somuchcandy', 'bigbreak90');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}