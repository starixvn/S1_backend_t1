<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510261725 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'breakaway',
				'english_name' => 'Break Away',
				'external_game_id' => 'Break Away',
			),
			array(
				'game_code' => 'rrjackandjill',
				'english_name' => 'RR Jack and Jill 96',
				'external_game_id' => 'RR Jack and Jill 96',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

		//Break Away 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'breakawayv90', 'english_name' => "Break Away 90", 'external_game_id' => "Break Away 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.breakawayv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Gold Factory
		$this->db->insert($this->tableName, array(
			'game_code' => 'goldfactory', 'english_name' => "Gold Factory", 'external_game_id' => "Gold Factory",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.goldfactory', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Thunderstruck
		$this->db->insert($this->tableName, array(
			'game_code' => 'thunderstruck', 'english_name' => "Thunderstruck", 'external_game_id' => "Thunderstruck",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.thunderstruck', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Progressive Mega
		$this->db->insert($this->tableName, array(
			'game_code' => 'progressivemega', 'english_name' => "Progressive Mega", 'external_game_id' => "Progressive Mega",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.progressivemega', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Progressive MM Isis
		$this->db->insert($this->tableName, array(
			'game_code' => 'progressivemmisis', 'english_name' => "Progressive MM Isis", 'external_game_id' => "Progressive MM Isis",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.progressivemmisis', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Progressive The Dark Knight
		$this->db->insert($this->tableName, array(
			'game_code' => 'progressivethedarkknight', 'english_name' => "Progressive The Dark Knight", 'external_game_id' => "Progressive The Dark Knight",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.progressivethedarkknight', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Burning Desire
		$this->db->insert($this->tableName, array(
			'game_code' => 'burningdesire', 'english_name' => "Burning Desire", 'external_game_id' => "Burning Desire",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.burningdesire', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Flash-Progressive Slots-Treasure Nile
		$this->db->insert($this->tableName, array(
			'game_code' => 'flashprogressiveslotstreasurenile', 'english_name' => "Flash-Progressive Slots-Treasure Nile", 'external_game_id' => "Flash-Progressive Slots-Treasure Nile",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.flashprogressiveslotstreasurenile', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//5 Reel Drive
		$this->db->insert($this->tableName, array(
			'game_code' => '5reeldriveflash', 'english_name' => "5 Reel Drive", 'external_game_id' => "5 Reel Drive",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.5reeldriveflash', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Spring break
		$this->db->insert($this->tableName, array(
			'game_code' => 'springbreakmobile', 'english_name' => "Spring break", 'external_game_id' => "Spring break",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.springbreakmobile', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('breakawayv90', 'goldfactory', 'thunderstruck', 'progressivemega', 'progressivemmisis',
			'progressivethedarkknight', 'burningdesire', 'flashprogressiveslotstreasurenile', '5reeldriveflash',
			'springbreakmobile');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}