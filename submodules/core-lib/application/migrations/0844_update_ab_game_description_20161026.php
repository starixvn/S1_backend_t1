<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ab_game_description_20161026 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$game_descriptions = array(
			array(
				'game_platform_id' => 29,
				'game_code' => '101',
				'english_name' => 'Baccarat',
				'game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '102',
				'english_name' => 'VIP Baccarat',
				'game_name' => '_json:{"1":"VIP Baccarat","2":"VIP 百家乐"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '103',
				'english_name' => 'Quick Baccarat',
				'game_name' => '_json:{"1":"Quick Baccarat","2":"急速百家乐"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '104',
				'english_name' => 'BidMe',
				'game_name' => '_json:{"1":"BidMe","2":"竞咪百家乐"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '201',
				'english_name' => 'Sicbo',
				'game_name' => '_json:{"1":"Sicbo","2":"骰宝"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '301',
				'english_name' => 'DragonTiger',
				'game_name' => '_json:{"1":"DragonTiger","2":"龙虎"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => '401',
				'english_name' => 'Roulette',
				'game_name' => '_json:{"1":"Roulette","2":"轮盘"}',
			),
			array(
				'game_platform_id' => 29,
				'game_code' => 'unknown',
				'english_name' => 'Unknown Game',
				'game_name' => '_json:{"1":"Unknown Game","2":"未知游戏"}',
			),
		);

		$this->db->where('game_platform_id', AB_API);
		$this->db->update_batch('game_description', $game_descriptions, 'game_code');

		$this->db->trans_complete();
	}

	public function down() {
	}
}
