<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201511112106 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//Voila
		$this->db->insert($this->tableName, array(
			'game_code' => 'voila', 'english_name' => "Voila", 'external_game_id' => "Voila",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.voila', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Kings of Cash 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'kingsofcash90', 'english_name' => "Kings of Cash 90", 'external_game_id' => "Kings of Cash 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.kingsofcash90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//One Arm Bandit
		$this->db->insert($this->tableName, array(
			'game_code' => 'onearmbandit', 'english_name' => "One Arm Bandit", 'external_game_id' => "One Arm Bandit",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.onearmbandit', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Dog Father
		$this->db->insert($this->tableName, array(
			'game_code' => 'dogfather', 'english_name' => "Dog Father", 'external_game_id' => "Dog Father",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.dogfather', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Franken Cash  90
		$this->db->insert($this->tableName, array(
			'game_code' => 'frankencash90', 'english_name' => "Franken Cash  90", 'external_game_id' => "Franken Cash  90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.frankencash90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('voila', 'kingsofcash90', 'onearmbandit', 'frankencash90');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}