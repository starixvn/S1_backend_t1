<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510301844 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'luckyfirecracker',
				'english_name' => 'Lucky Firecracker',
				'external_game_id' => 'Lucky Firecracker',
			),
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');

		//Stash of the Titans 90
		$this->db->insert($this->tableName, array(
			'game_code' => 'stashofthetitansv90', 'english_name' => "Stash of the Titans 90", 'external_game_id' => "Stash of the Titans 90",
			'game_platform_id' => MG_API, 'game_type_id' => 14,
			'game_name' => 'mg.StashoftheTitans', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 1, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

	}

	public function down() {
		$codes = array('stashofthetitansv90');
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', MG_API);
		$this->db->delete($this->tableName);
	}
}