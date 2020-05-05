<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_entwine_to_game_type_and_game_description_201606030740 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$this->db->trans_start();
		$data = array(
						array(
							'game_type' => 'Live Game',
							'game_type_lang' => 'entwine_live_games',
							'status' => self::FLAG_TRUE,
							'flag_show_in_site' => self::FLAG_TRUE,
							'game_description_list' => array(
								array(
									'game_name' => 'Baccarat Traditional',
									'english_name' => 'Baccarat Traditional',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '10001',
									'external_game_id' => '10001'
								),
								array(
									'game_name' => 'Baccarat Pair',
									'english_name' => 'Baccarat Pair',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '10002',
									'external_game_id' => '10002'
								),
								array(
									'game_name' => 'Dragon/Tiger',
									'english_name' => 'Dragon/Tiger',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '20001',
									'external_game_id' => '20001'
								),
								array(
									'game_name' => 'VIP Baccarat',
									'english_name' => 'VIP Baccarat',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '30001',
									'external_game_id' => '30001'
								),
								array(
									'game_name' => 'Asian Roulette',
									'english_name' => 'Asian Roulette',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '50001',
									'external_game_id' => '50001'
								),
								array(
									'game_name' => 'International Roulette',
									'english_name' => 'International Roulette',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '50002',
									'external_game_id' => '50002'
								),
								array(
									'game_name' => 'Roulette',
									'english_name' => 'Roulette',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '50003',
									'external_game_id' => '50003'
								),
								array(
									'game_name' => 'Sic Bo',
									'english_name' => 'Sic Bo',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '60001',
									'external_game_id' => '60001'
								),
								array(
									'game_name' => 'Super Baccarat',
									'english_name' => 'Super Baccarat',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '90001',
									'external_game_id' => '90001'
								),
								array(
									'game_name' => 'Super 6 Baccarat',
									'english_name' => 'Super 6 Baccarat',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '90002',
									'external_game_id' => '90002'
								),
								array(
									'game_name' => 'Dragon Bonus Baccarat',
									'english_name' => 'Dragon Bonus Baccarat',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '90003',
									'external_game_id' => '90003'
								),
								array(
									'game_name' => 'Points Baccarat',
									'english_name' => 'Points Baccarat',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '90004',
									'external_game_id' => '90004'
								),
								array(
									'game_name' => 'Blackjack',
									'english_name' => 'Blackjack',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => '100001',
									'external_game_id' => '100001'
								)
							),
						),
						array(
							'game_type' => 'unknown',
							'game_type_lang' => 'entwine.unknown',
							'status' => self::FLAG_TRUE,
							'flag_show_in_site' => self::FLAG_FALSE,
							'game_description_list' => array(
								array(
									'game_name' => 'entwine.unknown',
									'english_name' => 'entwine.unknown',
									'html_five_enabled' =>0,
									'flash_enabled' =>1,
									'game_code' => 'unknown',
									'external_game_id' => 'unknown'
								)
							)
						),
					);

		$game_description_list = array();
		foreach ($data as $game_type) {

			$this->db->insert('game_type', array(
				'game_platform_id' => ENTWINE_API,
				'game_type' => $game_type['game_type'],
				'game_type_lang' => $game_type['game_type_lang'],
				'status' => $game_type['status'],
				'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

			$game_type_id = $this->db->insert_id();
			foreach ($game_type['game_description_list'] as $game_description) {
				$game_description_list[] = array_merge(array(
					'game_platform_id' => ENTWINE_API,
					'game_type_id' => $game_type_id,
					), $game_description);
			}

		}

		$this->db->insert_batch('game_description', $game_description_list);
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' => ENTWINE_API));
		$this->db->delete('game_description', array('game_platform_id' => ENTWINE_API));
		$this->db->trans_complete();
	}
}