<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gd_live_game_type_and_game_description_201605051800 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
	

		$this->db->trans_start();

			//insert to game_description
			$data = array(
				array(
					'game_type' => 'GD Live Dealer',
					'game_type_lang' => 'gd_livedealer',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
							//Video Slot
							array('game_name' => 'GD Live Baccarat',
							'english_name' => 'GD Live Baccarat',
							'external_game_id' => 'gd.Baccarat',
							'game_code' => 'Baccarat'
							),
							array('game_name' => 'GD Live Roulette',
							'english_name' => 'GD Live Roulette',
							'external_game_id' => 'gd.Roulette',
							'game_code' => 'Roulette'
							),
							array('game_name' => 'GD Live Blackjack',
							'english_name' => 'GD Live Blackjack',
							'external_game_id' => 'gd.Blackjack',
							'game_code' => 'Blackjack'
							),
							array('game_name' => 'GD Live Slot Game',
							'english_name' => 'GD Live Slot Game',
							'external_game_id' => 'gd.Slotgame',
							'game_code' => 'Slotgame'
							),
						),
					),

				);



			$game_description_list = array();
			foreach ($data as $game_type) {
				$this->db->insert('game_type', array(
					'game_platform_id' => GD_API,
					'game_type' => $game_type['game_type'],
					'game_type_lang' => $game_type['game_type_lang'],
					'status' => $game_type['status'],
					'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

				$game_type_id = $this->db->insert_id();
				foreach ($game_type['game_description_list'] as $game_description) {
					$game_description_list[] = array_merge(array(
						'game_platform_id' => GD_API,
						'game_type_id' => $game_type_id,
					), $game_description);
				}
			}

			$this->db->insert_batch('game_description', $game_description_list);
			$this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' =>  GD_API, 'game_type_lang' => 'gd_livedealer'));
		$this->db->delete('game_description', array('game_platform_id' =>  GD_API, 'game_code' => 'Baccarat'));
		$this->db->delete('game_description', array('game_platform_id' =>  GD_API, 'game_code' => 'Roulette'));
		$this->db->delete('game_description', array('game_platform_id' =>  GD_API, 'game_code' => 'Blackjack'));
		$this->db->delete('game_description', array('game_platform_id' =>  GD_API, 'game_code' => 'Slotgame'));
		$this->db->trans_complete();
	}
}