<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510291202 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'chainmailnew',
				'english_name' => 'Chain Mail New',
				'external_game_id' => 'Chain Mail New',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

		//5 Reel Drive 90
		$this->db->insert($this->tableName, array(
			'game_code' => '5reeldrivev90', 'english_name' => "5 Reel Drive 90", 'external_game_id' => "5 Reel Drive 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.5reeldrivev90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Ladies Nite 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'ladiesnite90', 'english_name' => "Ladies Nite 90", 'external_game_id' => "Ladies Nite 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.ladiesnite90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Double O Cash 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'doubleocashv90', 'english_name' => "Double O Cash 90", 'external_game_id' => "Double O Cash 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.doubleocashv90', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		//Big Kahuna - Snakes and Ladders (Big Kahuna - Snakes and Ladders)
		$this->db->insert($this->tableName, array(
			'game_code' => 'bigkahunasnakesandladders', 'english_name' => "Big Kahuna - Snakes and Ladders (Big Kahuna - Snakes and Ladders)", 'external_game_id' => "Big Kahuna - Snakes and Ladders (Big Kahuna - Snakes and Ladders)",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.bigkahunasnakesandladders', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('5reeldrivev90', 'ladiesnite90', 'doubleocashv90', 'bigkahunasnakesandladders');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}