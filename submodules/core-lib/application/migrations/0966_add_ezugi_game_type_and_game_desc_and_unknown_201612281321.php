<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ezugi_game_type_and_game_desc_and_unknown_201612281321 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
	

		$this->db->trans_start();

			//insert to game_description
			$data = array(
				array(
					'game_type' => 'BlackJack',
					'game_type_lang' => '_json:{"1":"BlackJack","2":"21点"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"BlackJack","2":"21点"}',
							'english_name' => 'BlackJack',
							'external_game_id' => '1',
							'game_code' => '1'
						)
					)
				),
				array(
					'game_type' => 'Baccarat',
					'game_type_lang' => '_json:{"1":"Baccarat","2":"百家乐"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat","2":"百家乐"}',
							'english_name' => 'Baccarat',
							'external_game_id' => '2',
							'game_code' => '2'
						)
					)
				),
				array(
					'game_type' => 'Roulette',
					'game_type_lang' => '_json:{"1":"Roulette","2":"轮盘"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Roulette","2":"轮盘"}',
							'english_name' => 'Roulette',
							'external_game_id' => '3',
							'game_code' => '3'
						)
					)
				),
				array(
					'game_type' => 'Lottery',
					'game_type_lang' => '_json:{"1":"Lottery","2":"彩票"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Lottery","2":"彩票"}',
							'english_name' => 'Lottery',
							'external_game_id' => '4',
							'game_code' => '4'
						)
					)
				),
				array(
					'game_type' => 'Hybrid Blackjack',
					'game_type_lang' => '_json:{"1":"Hybrid Blackjack","2":"Hybrid 21点"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Hybrid Blackjack","2":"Hybrid 21点"}',
							'english_name' => 'Hybrid Blackjack',
							'external_game_id' => '5',
							'game_code' => '5'
						)
					)
				),
				array(
					'game_type' => 'Keno',
					'game_type_lang' => '_json:{"1":"Keno","2":"基诺"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Keno","2":"基诺"}',
							'english_name' => 'Keno',
							'external_game_id' => '6',
							'game_code' => '6'
						)
					)
				),
				array(
					'game_type' => 'Auto Roulette',
					'game_type_lang' => '_json:{"1":"Auto Roulette","2":"自動輪盤"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Auto Roulette","2":"自動輪盤"}',
							'english_name' => 'Auto Roulette',
							'external_game_id' => '7',
							'game_code' => '7'
						)
					)
				),
				array(
					'game_type' => 'Wheel Of Dice',
					'game_type_lang' => '_json:{"1":"Wheel Of Dice","2":"Wheel Of Dice"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Wheel Of Dice","2":"Wheel Of Dice"}',
							'english_name' => 'Wheel Of Dice',
							'external_game_id' => '8',
							'game_code' => '8'
						)
					)
				),
				array(
					'game_type' => 'Sedie',
					'game_type_lang' => '_json:{"1":"Sedie","2":"Sedie"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Sedie","2":"Sedie"}',
							'english_name' => 'Sedie',
							'external_game_id' => '9',
							'game_code' => '9'
						)
					)
				),
				array(
					'game_type' => 'American Blackjack',
					'game_type_lang' => '_json:{"1":"American Blackjack","2":"美式21点"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"American Blackjack","2":"美式21点"}',
							'english_name' => 'American Blackjack',
							'external_game_id' => '10',
							'game_code' => '10'
						)
					)
				),
				array(
					'game_type' => 'American Hybrid Blackjack',
					'game_type_lang' => '_json:{"1":"American Hybrid Blackjack","2":"American Hybrid Blackjack"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"American Hybrid Blackjack","2":"American Hybrid Blackjack"}',
							'english_name' => 'American Hybrid Blackjack',
							'external_game_id' => '11',
							'game_code' => '11'
						)
					)
				),
				array(
					'game_type' => 'Sic Bo',
					'game_type_lang' => '_json:{"1":"Sic Bo","2":"骰宝"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Sic Bo","2":"骰宝"}',
							'english_name' => 'Sic Bo',
							'external_game_id' => '14',
							'game_code' => '14'
						)
					)
				),
				array(
					'game_type' => 'Casino Holdem',
					'game_type_lang' => '_json:{"1":"Casino Holdem","2":"Casino Holdem"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Casino Holdem","2":"Casino Holdem"}',
							'english_name' => 'Casino Holdem',
							'external_game_id' => '15',
							'game_code' => '15'
						)
					)
				),
				array(
					'game_type' => 'Baccarat KO',
					'game_type_lang' => '_json:{"1":"Baccarat KO","2":"Baccarat KO"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat KO","2":"Baccarat KO"}',
							'english_name' => 'Baccarat KO',
							'external_game_id' => '20',
							'game_code' => '20'
						)
					)
				),
				array(
					'game_type' => 'Baccarat super 6',
					'game_type_lang' => '_json:{"1":"Baccarat super 6","2":"Baccarat super 6"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat super 6","2":"Baccarat super 6"}',
							'english_name' => 'Baccarat super 6',
							'external_game_id' => '21',
							'game_code' => '21'
						)
					)
				),
				array(
					'game_type' => 'Baccarat multi-seat',
					'game_type_lang' => '_json:{"1":"Baccarat multi-seat","2":"Baccarat multi-seat"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat multi-seat","2":"Baccarat multi-seat"}',
							'english_name' => 'Baccarat multi-seat',
							'external_game_id' => '22',
							'game_code' => '22'
						)
					)
				),
				array(
					'game_type' => 'Baccarat Insurance',
					'game_type_lang' => '_json:{"1":"Baccarat Insurance","2":"Baccarat Insurance"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat Insurance","2":"Baccarat Insurance"}',
							'english_name' => 'Baccarat Insurance',
							'external_game_id' => '23',
							'game_code' => '23'
						)
					)
				),
				array(
					'game_type' => 'Baccarat Dragon Tiger',
					'game_type_lang' => '_json:{"1":"Baccarat Dragon Tiger","2":"Baccarat Dragon Tiger"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat Dragon Tiger","2":"Baccarat Dragon Tiger"}',
							'english_name' => 'Baccarat Dragon Tiger',
							'external_game_id' => '24',
							'game_code' => '24'
						)
					)
				),
				array(
					'game_type' => 'Baccarat No Commission',
					'game_type_lang' => '_json:{"1":"Baccarat No Commission","2":"无佣百家乐"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat No Commission","2":"无佣百家乐"}',
							'english_name' => 'Baccarat No Commission',
							'external_game_id' => '25',
							'game_code' => '25'
						)
					)
				),
				array(
					'game_type' => 'Baccarat Dragon Bonus',
					'game_type_lang' => '_json:{"1":"Baccarat Dragon Bonus","2":"Baccarat Dragon Bonus"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Baccarat Dragon Bonus","2":"Baccarat Dragon Bonus"}',
							'english_name' => 'Baccarat Dragon Bonus',
							'external_game_id' => '26',
							'game_code' => '26'
						)
					)
				),
				array(
					'game_type' => 'American Roulette',
					'game_type_lang' => '_json:{"1":"American Roulette","2":"美式轮盘赌"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"American Roulette","2":"美式轮盘赌"}',
							'english_name' => 'American Roulette',
							'external_game_id' => '31',
							'game_code' => '31'
						)
					)
				),
				array(
					'game_type' => 'Lottery OMS',
					'game_type_lang' => '_json:{"1":"Lottery OMS","2":"彩票OMS"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Lottery OMS","2":"彩票OMS"}',
							'english_name' => 'Lottery OMS',
							'external_game_id' => '34',
							'game_code' => '34'
						)
					)
				),
				array(
					'game_type' => 'Keno OMS',
					'game_type_lang' => '_json:{"1":"Keno OMS","2":"基诺OMS"}',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"Keno OMS","2":"基诺OMS"}',
							'english_name' => 'Keno OMS',
							'external_game_id' => '34',
							'game_code' => '34'
						)
					)
				),
				array(
					'game_type' => 'EZUGI UNKNOWN',
					'game_type_lang' => 'EZUGI UNKNOWN',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"EZUGI UNKNOWN GAME","2":"EZUGI 未知游戏"}',	
							'english_name' => 'EZUGI UNKNOWN GAME',
							'external_game_id' => 'unknown',
							'game_code' => 'unknown'
						)
					)
				)
			);
			$game_description_list = array();
			foreach ($data as $game_type) {
				$this->db->insert('game_type', array(
					'game_platform_id' => EZUGI_API,
					'game_type' => $game_type['game_type'],
					'game_type_lang' => $game_type['game_type_lang'],
					'status' => $game_type['status'],
					'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

				$game_type_id = $this->db->insert_id();
				foreach ($game_type['game_description_list'] as $game_description) {
					$game_description_list[] = array_merge(array(
						'game_platform_id' => EZUGI_API,
						'game_type_id' => $game_type_id,
					), $game_description);
				}
			}

			$this->db->insert_batch('game_description', $game_description_list);
			$this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' =>  EZUGI_API));
		$this->db->delete('game_description', array('game_platform_id' =>  EZUGI_API));
		$this->db->trans_complete();
	}
}