<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_clientid_and_moduleid_to_201511021056 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//The Grand Journey
		$this->db->insert($this->tableName, array(
			'game_code' => 'thegrandjourney', 'english_name' => "The Grand Journey", 'external_game_id' => "The Grand Journey",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.thegrandjourney', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Jurassic Big Reels
		$this->db->insert($this->tableName, array(
			'game_code' => 'jurassicbigreels', 'english_name' => "Jurassic Big Reels", 'external_game_id' => "Jurassic Big Reels",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.jurassicbigreels', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Tomb Raide
		$this->db->insert($this->tableName, array(
			'game_code' => 'tombraide', 'english_name' => "Tomb Raide", 'external_game_id' => "Tomb Raide",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tombraide', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Alaskan Fishing 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'alaskanfishing90', 'english_name' => "Alaskan Fishing 90", 'external_game_id' => "Alaskan Fishing 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.alaskanfishing90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Curry in a Hurry
		$this->db->insert($this->tableName, array(
			'game_code' => 'curryinahurry', 'english_name' => "Curry in a Hurry", 'external_game_id' => "Curry in a Hurry",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.curryinahurry', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Gift Rap 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'giftrap90', 'english_name' => "Gift Rap 90", 'external_game_id' => "Gift Rap 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.giftrap90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Fruit Fiesta 5 Reel
		$this->db->insert($this->tableName, array(
			'game_code' => 'fruitfiesta5reel', 'english_name' => "Fruit Fiesta 5 Reel", 'external_game_id' => "Fruit Fiesta 5 Reel",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.fruitfiesta5reel', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Tigers Eye
		$this->db->insert($this->tableName, array(
			'game_code' => 'tigerseye', 'english_name' => "Tigers Eye", 'external_game_id' => "Tigers Eye",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tigerseye', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Mermaids Millions
		$this->db->insert($this->tableName, array(
			'game_code' => 'mermaidsmillions', 'english_name' => "Mermaids Millions", 'external_game_id' => "Mermaids Millions",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.mermaidsmillions', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Diamond Sevens
		$this->db->insert($this->tableName, array(
			'game_code' => 'diamondsevens', 'english_name' => "Diamond Sevens", 'external_game_id' => "Diamond Sevens",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.diamondsevens', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Liquid Gold
		$this->db->insert($this->tableName, array(
			'game_code' => 'liquidgold', 'english_name' => "Liquid Gold", 'external_game_id' => "Liquid Gold",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.liquidgold', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Cashapillar 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'cashapillar90', 'english_name' => "Cashapillar 90", 'external_game_id' => "Cashapillar 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cashapillar90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Karate Pig 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'karatepig90', 'english_name' => "Karate Pig 90", 'external_game_id' => "Karate Pig 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.karatepig90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Wheel Of Wealth
		$this->db->insert($this->tableName, array(
			'game_code' => 'wheelofwealth', 'english_name' => "Wheel Of Wealth", 'external_game_id' => "Wheel Of Wealth",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.wheelofwealth', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Tally Ho 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'tallyho90', 'english_name' => "Tally Ho 90", 'external_game_id' => "Tally Ho 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.tallyho90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Lots of Loot
		$this->db->insert($this->tableName, array(
			'game_code' => 'lotsofloot', 'english_name' => "Lots of Loot", 'external_game_id' => "Lots of Loot",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.lotsofloot', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Cash Splash Progressive
		$this->db->insert($this->tableName, array(
			'game_code' => 'cashsplashprogressive', 'english_name' => "Cash Splash Progressive", 'external_game_id' => "Cash Splash Progressive",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.cashsplashprogressive', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Kathmandu 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'kathmandu90', 'english_name' => "Kathmandu 90", 'external_game_id' => "Kathmandu 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.kathmandu90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Lucky Witch 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'luckywitch90', 'english_name' => "Lucky Witch 90", 'external_game_id' => "Lucky Witch 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.luckywitch90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Isis 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'isis90', 'english_name' => "Isis 90", 'external_game_id' => "Isis 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.isis90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Jolly Jester 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'jollyjester90', 'english_name' => "Jolly Jester 90", 'external_game_id' => "Jolly Jester 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.jollyjester90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Eagle's Wings
		$this->db->insert($this->tableName, array(
			'game_code' => 'eagleswings', 'english_name' => "Eagle's Wings", 'external_game_id' => "Eagle's Wings",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.eagleswings', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Bars & Stripes
		$this->db->insert($this->tableName, array(
			'game_code' => 'barsstripes', 'english_name' => "Bars & Stripes", 'external_game_id' => "Bars & Stripes",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.barsstripes', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Free Spirit
		$this->db->insert($this->tableName, array(
			'game_code' => 'freespirit', 'english_name' => "Free Spirit", 'external_game_id' => "Free Spirit",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.freespirit', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		$this->dbforge->add_column($this->tableName, array(
			'clientid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'moduleid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'clientid');
		$this->dbforge->drop_column($this->tableName, 'moduleid');
		$codes = array('thegrandjourney', 'jurassicbigreels', 'tombraide',
			'alaskanfishing90', 'curryinahurry', 'giftrap90',
			'fruitfiesta5reel', 'tigerseye', 'mermaidsmillions',
			'diamondsevens', 'liquidgold', 'cashapillar90',
			'karatepig90', 'wheelofwealth', 'tallyho90',
			'lotsofloot', 'cashsplashprogressive', 'kathmandu90',
			'luckywitch90', 'isis90', 'jollyjester90',
			'eagleswings', 'barsstripes', 'freespirit',
		);
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}