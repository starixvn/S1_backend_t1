<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510280241 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'jackpotdeuces',
				'english_name' => 'Prog Poker - Jackpot Deuces',
				'external_game_id' => 'Prog Poker - Jackpot Deuces',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

		//Pick 'n Switch
		$this->db->insert($this->tableName, array(
			'game_code' => 'picknswitch', 'english_name' => "Pick 'n Switch", 'external_game_id' => "Pick 'n Switch",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.picknswitch', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Flash-Progressive Cyberstud
		$this->db->insert($this->tableName, array(
			'game_code' => 'flashprogressivecyberstud', 'english_name' => "Flash-Progressive Cyberstud", 'external_game_id' => "Flash-Progressive Cyberstud",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.flashprogressivecyberstud', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//European Roulette Gold
		$this->db->insert($this->tableName, array(
			'game_code' => 'europeanroulettegold', 'english_name' => "European Roulette Gold", 'external_game_id' => "European Roulette Gold",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.europeanroulettegold', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('picknswitch', 'flashprogressivecyberstud', 'europeanroulettegold');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}