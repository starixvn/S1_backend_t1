<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510301801 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//Card Selector - Super Zeroes
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorsuperzeroes', 'english_name' => "Card Selector - Super Zeroes", 'external_game_id' => "Card Selector - Super Zeroes",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubySuperZeroes', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Granny Prix
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorgrannyprix', 'english_name' => "Card Selector - Granny Prix", 'external_game_id' => "Card Selector - Granny Prix",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyGrannyPrix', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Slam Funk
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorslamfunk', 'english_name' => "Card Selector - Slam Funk", 'external_game_id' => "Card Selector - Slam Funk",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubySlamFunk', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Freezing Fuzzballs
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorfreezingfuzzballs', 'english_name' => "Card Selector - Freezing Fuzzballs", 'external_game_id' => "Card Selector - Freezing Fuzzballs",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyFreezingFuzzballs', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Golden Ghouls
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorgoldenghouls', 'english_name' => "Card Selector - Golden Ghouls", 'external_game_id' => "Card Selector - Golden Ghouls",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyGoldenGhouls', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Wild Champions
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorwildchampions', 'english_name' => "Card Selector - Wild Champions", 'external_game_id' => "Card Selector - Wild Champions",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyWildChampions', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Dawn of the Bread
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectordawnofthebread', 'english_name' => "Card Selector - Dawn of the Bread", 'external_game_id' => "Card Selector - Dawn of the Bread",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyDawnOfTheBread', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Mumbai Magic
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectormumbaimagic', 'english_name' => "Card Selector - Mumbai Magic", 'external_game_id' => "Card Selector - Mumbai Magic",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyMumbaiMagic', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Cashapillar
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorcashapillar', 'english_name' => "Card Selector - Cashapillar", 'external_game_id' => "Card Selector - Cashapillar",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyCashapillar', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Halloweenies
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorhalloweenies', 'english_name' => "Card Selector - Halloweenies", 'external_game_id' => "Card Selector - Halloweenies",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyHalloweenies', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Card Selector - Big Break
		$this->db->insert($this->tableName, array(
			'game_code' => 'cardselectorbigbreak', 'english_name' => "Card Selector - Big Break", 'external_game_id' => "Card Selector - Big Break",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubyBigBreak', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Summertime 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'summertime90', 'english_name' => "Summertime 90", 'external_game_id' => "Summertime 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.RubySummertime', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('cardselectorsuperzeroes', 'cardselectorgrannyprix', 'cardselectorslamfunk',
			'cardselectorfreezingfuzzballs', 'cardselectorgoldenghouls', 'cardselectorwildchampions',
			'cardselectordawnofthebread', 'cardselectormumbaimagic', 'cardselectorcashapillar',
			'cardselectorhalloweenies', 'cardselectorbigbreak', 'summertime90');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}