<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510301241 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'gophergoldv90',
				'english_name' => 'Gopher Gold_ 90',
				'external_game_id' => 'Gopher Gold_ 90',
			),
			array(
				'game_code' => 'bigkahunav90',
				'english_name' => 'Big Kahuna 90',
				'external_game_id' => 'Big Kahuna 90',
			),
			array(
				'game_code' => 'luckyfirecracker',
				'english_name' => 'Lucky Firecracker',
				'external_game_id' => 'Lucky Firecracker',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

		//Tomb Raider
		$this->db->insert($this->tableName, array(
			'game_code' => 'tombraider', 'english_name' => "Tomb Raider", 'external_game_id' => "Tomb Raider",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tombraider', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Bush Telegraph 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'bushtelegraphv90', 'english_name' => "Bush Telegraph 90", 'external_game_id' => "Bush Telegraph 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.bushtelegraphv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Sterlingilver 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'sterlingilver90', 'english_name' => "Sterlingilver 90", 'external_game_id' => "Sterlingilver 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.sterlingilver90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Megaspin - Break da Bank Again 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'megaspinbreakdabankagainv90', 'english_name' => "Megaspin - Break da Bank Again 90", 'external_game_id' => "Megaspin - Break da Bank Again 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.megaspinbreakdabankagainv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Untamed - Bengal Tiger 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'untamedbengaltigerv90', 'english_name' => "Untamed - Bengal Tiger 90", 'external_game_id' => "Untamed - Bengal Tiger 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.untamedbengaltigerv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Pure Platinum 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'pureplatinumv90', 'english_name' => "Pure Platinum 90", 'external_game_id' => "Pure Platinum 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.pureplatinumv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('tombraider', 'bushtelegraphv90', 'sterlingilver90', 'megaspinbreakdabankagainv90', 'untamedbengaltigerv90', 'pureplatinumv90');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}