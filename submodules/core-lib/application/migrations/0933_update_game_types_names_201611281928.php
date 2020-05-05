<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_types_names_201611281928 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$game_types_777 = array(
			'_json:{"1":"Adult Slot","2":"真人荷官"}' => array(
				'game_type' => '_json:{"1":"Adult Slot","2":"成人老虎机"}',
				'game_type_lang' => '_json:{"1":"Adult Slot","2":"成人老虎机"}',
			),
			'_json:{"1":"HD Slot","2":"真人荷官"}' => array(
				'game_type' => '_json:{"1":"HD Slot","2":"高清老虎机"}',
				'game_type_lang' => '_json:{"1":"HD Slot","2":"高清老虎机"}',
			),
			'_json:{"1":"Table Game","2":"真人荷官"}' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Classic Slot' => array(
				'game_type' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
				'game_type_lang' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
			)
		);

		$this->db->trans_start();

		foreach ($game_types_777 as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', SEVEN77_API)
					 ->update('game_type', $game_type_data);

		}

		$this->db->trans_complete();
	}

	public function down() {
	}
}
