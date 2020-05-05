<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_types_20161024 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$game_types = array(
			'百家乐' => array(
				'game_type' => '_json:{"1":"Baccarat","2":"百家乐"}',
				'game_type_lang' => '_json:{"1":"Baccarat","2":"百家乐"}',
			),
			'龙虎' => array(
				'game_type' => '_json:{"1":"Dragon Tiger","2":"龙虎"}',
				'game_type_lang' => '_json:{"1":"Dragon Tiger","2":"龙虎"}',
			),
			'骰宝' => array(
				'game_type' => '_json:{"1":"Sicbo","2":"骰宝"}',
				'game_type_lang' => '_json:{"1":"Sicbo","2":"骰宝"}',
			),
			'轮盘' => array(
				'game_type' => '_json:{"1":"Roulette Wheel","2":"轮盘"}',
				'game_type_lang' => '_json:{"1":"Roulette Wheel","2":"轮盘"}',
			),
			'水果机' => array(
				'game_type' => '_json:{"1":"Fruit Machine","2":"水果机"}',
				'game_type_lang' => '_json:{"1":"Fruit Machine","2":"水果机"}',
			),
		);

		$this->db->trans_start();
		foreach ($game_types as $game_type => $game_type_data) {
			$this->db->where_in('game_platform_id', [EBET_API,EBET2_API])->where('game_type', $game_type)->update('game_type', $game_type_data);
		}
		$this->db->trans_complete();
	}

	public function down() {
	}
}
