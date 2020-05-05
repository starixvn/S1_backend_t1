<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ab_to_game_type_20160402 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$this->db->trans_start();

		$data = array(
			array(
				'game_type' 			=> 'Live Games',
				'game_type_lang' 		=> 'ab_live_games',
				'status' 				=> self::FLAG_TRUE,
				'flag_show_in_site' 	=> self::FLAG_TRUE,
				'game_description_list' => array(
					array(
						'game_name' 		=> 'ab.baccarat',
						'game_code' 		=> '101',
						'english_name' 		=> 'Baccarat',
						'external_game_id' 	=> '101',
					),
					array(
						'game_name' 		=> 'ab.vipbaccarat',
						'game_code' 		=> '102',
						'english_name' 		=> 'VIP Baccarat',
						'external_game_id' 	=> '102',
					),
					array(
						'game_name' 		=> 'ab.quickbaccarat',
						'game_code' 		=> '103',
						'english_name' 		=> 'Quick Baccarat',
						'external_game_id' 	=> '103',
					),
					array(
						'game_name' 		=> 'ab.bidme',
						'game_code' 		=> '104',
						'english_name' 		=> 'BidMe',
						'external_game_id' 	=> '104',
					),
					array(
						'game_name' 		=> 'ab.sicbo',
						'game_code' 		=> '201',
						'english_name' 		=> 'Sicbo',
						'external_game_id' 	=> '201',
					),
					array(
						'game_name' 		=> 'ab.dragontiger',
						'game_code' 		=> '301',
						'english_name' 		=> 'DragonTiger',
						'external_game_id' 	=> '301',
					),
					array(
						'game_name' 		=> 'ab.roulette',
						'game_code' 		=> '401',
						'english_name' 		=> 'Roulette',
						'external_game_id' 	=> '401',
					),
				),
			),
		);

		$game_description_list = array();
		foreach ($data as $game_type) {

			$this->db->insert('game_type', array(
				'game_platform_id' 		=> AB_API,
				'game_type' 			=> $game_type['game_type'],
				'game_type_lang' 		=> $game_type['game_type_lang'],
				'status' 				=> $game_type['status'],
				'flag_show_in_site' 	=> $game_type['flag_show_in_site'],
			));

			$game_type_id = $this->db->insert_id();
			foreach ($game_type['game_description_list'] as $game_description) {
				$game_description_list[] = array_merge(array(
					'game_platform_id' 	=> AB_API,
					'game_type_id' 		=> $game_type_id,
				), $game_description);
			}

		}

		$this->db->insert_batch('game_description', $game_description_list);
		$this->db->trans_complete();

	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' => AB_API));
		$this->db->delete('game_description', array('game_platform_id' => AB_API));
		$this->db->trans_complete();
	}
}