<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_game_type_and_game_desc_and_unknown_201610151453 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
	

		$this->db->trans_start();

			//insert to game_description
			$data = array(
				array(
					'game_type' => 'BETEAST LIVE CASINO',
					'game_type_lang' => 'BETEAST LIVE CASINO',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '百家乐',
							'english_name' => 'Baccarat',
							'external_game_id' => 'L-N-baccarat',
							'game_code' => 'L-N-baccarat'
						),
						array(
							'game_name' => '超级六点百家乐',
							'english_name' => 'Super Baccarat',
							'external_game_id' => 'L-S-baccarat',
							'game_code' => 'L-S-baccarat'
						),
						array(
							'game_name' => '轮盘',
							'english_name' => 'Roulette',
							'external_game_id' => 'L-N-roulette',
							'game_code' => 'L-N-roulette'
						),
						array(
							'game_name' => '骰宝',
							'english_name' => 'Sic Bo',
							'external_game_id' => 'L-N-sicbo',
							'game_code' => 'L-N-sicbo'
						),
						array(
							'game_name' => '龙虎',
							'english_name' => 'Dragon and Tiger',
							'external_game_id' => 'L-N-dnt',
							'game_code' => 'L-N-dnt'
						),
						array(
							'game_name' => '彩票',
							'english_name' => 'Lotto',
							'external_game_id' => 'L-N-lotto',
							'game_code' => 'L-N-lotto'
						)
					)
				),
				array(
					'game_type' => 'BETEAST EGAME CASINO',
					'game_type_lang' => 'BETEAST EGAME CASINO',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_TRUE,
					'game_description_list' => array(
						array(
							'game_name' => '百家乐',
							'english_name' => 'Baccarat',
							'external_game_id' => 'E-N-baccarat',
							'game_code' => 'E-N-baccarat'
						),
						array(
							'game_name' => '超级六点百家乐',
							'english_name' => 'Super Baccarat',
							'external_game_id' => 'E-S-baccarat',
							'game_code' => 'E-S-baccarat'
						),
						array(
							'game_name' => '轮盘',
							'english_name' => 'Roulette',
							'external_game_id' => 'E-N-roulette',
							'game_code' => 'E-N-roulette'
						),
						array(
							'game_name' => '骰宝',
							'english_name' => 'Sic Bo',
							'external_game_id' => 'E-N-sicbo',
							'game_code' => 'E-N-sicbo'
						),
						array(
							'game_name' => '龙虎',
							'english_name' => 'Dragon and Tiger',
							'external_game_id' => 'E-N-dnt',
							'game_code' => 'E-N-dnt'
						)
					)
				),
				array(
					'game_type' => 'BETEAST UNKNOWN GAME',
					'game_type_lang' => 'BETEAST UNKNOWN GAME',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
					'game_description_list' => array(
						array(
							'game_name' => 'BETEAST 未知游戏',
							'english_name' => 'BETEAST UNKNOWN GAME',
							'external_game_id' => 'unknown',
							'game_code' => 'unknown'
						)
					)
				)
			);

			$game_description_list = array();
			foreach ($data as $game_type) {
				$this->db->insert('game_type', array(
					'game_platform_id' => BETEAST_API,
					'game_type' => $game_type['game_type'],
					'game_type_lang' => $game_type['game_type_lang'],
					'status' => $game_type['status'],
					'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

				$game_type_id = $this->db->insert_id();
				foreach ($game_type['game_description_list'] as $game_description) {
					$game_description_list[] = array_merge(array(
						'game_platform_id' => BETEAST_API,
						'game_type_id' => $game_type_id,
					), $game_description);
				}
			}

			$this->db->insert_batch('game_description', $game_description_list);
			$this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' =>  BETEAST_API));
		$this->db->delete('game_description', array('game_platform_id' =>  BETEAST_API));
		$this->db->trans_complete();
	}
}