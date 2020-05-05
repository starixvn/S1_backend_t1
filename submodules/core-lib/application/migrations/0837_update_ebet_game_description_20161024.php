<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_description_20161024 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$game_descriptions = array(
			array(
				'game_code' => '1',
				'game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
				'english_name' => 'Baccarat',
			),
			array(
				'game_code' => '2',
				'game_name' => '_json:{"1":"Dragon Tiger","2":"龙虎"}',
				'english_name' => 'Dragon Tiger',
			),
			array(
				'game_code' => '3',
				'game_name' => '_json:{"1":"Sicbo","2":"骰宝"}',
				'english_name' => 'Sicbo',
			),
			array(
				'game_code' => '4',
				'game_name' => '_json:{"1":"Roulette Wheel","2":"轮盘"}',
				'english_name' => 'Roulette Wheel',
			),
			array(
				'game_code' => '5',
				'game_name' => '_json:{"1":"Fruit Machine","2":"水果机"}',
				'english_name' => 'Fruit Machine',
			),
		);

		$this->db->where('game_platform_id', EBET_API);
		$this->db->update_batch('game_description', $game_descriptions, 'game_code');

		$this->db->where('game_platform_id', EBET2_API);
		$this->db->update_batch('game_description', $game_descriptions, 'game_code');

		$this->db->trans_complete();
	}

	public function down() {
	}
}
