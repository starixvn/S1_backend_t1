<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_pt_game_name_in_game_description_201511032151 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		//Jackpot Giant Jackpot 0.01$
		$this->db->insert($this->tableName, array(
			'game_code' => 'jpgt1', 'english_name' => "Jackpot Giant", 'external_game_id' => "jpgt1",
			'game_platform_id' => PT_API, 'game_type_id' => 7,
			'game_name' => 'pt.jpgt', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$codes = array('jpgt1');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}