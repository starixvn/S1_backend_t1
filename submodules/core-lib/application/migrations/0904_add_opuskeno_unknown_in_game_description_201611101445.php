<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_opuskeno_unknown_in_game_description_201611101445 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	
	public function up() {
		$this->db->trans_start();
		$data = array(
					array(
						'game_type' => ' UNKNOWN',
						'game_type_lang' => 'OPUS KENO UNKNOWN',
						'status' => self::FLAG_TRUE,
						'flag_show_in_site' => self::FLAG_FALSE,
						'game_description_list' => array(
							array(
								'game_name' => '_json:{"1":"OPUS KENO UNKNOWN GAME","2":"OPUS KENO 未知游戏"}',	
								'english_name' => 'OPUS KENO UNKNOWN GAME',
								'external_game_id' => 'unknown',
								'game_code' => 'unknown'
							)
						)
					)
				);

		$game_description_list = array();
		foreach ($data as $game_type) {
			$this->db->insert('game_type', array(
				'game_platform_id' => OPUS_KENO_API,
				'game_type' => $game_type['game_type'],
				'game_type_lang' => $game_type['game_type_lang'],
				'status' => $game_type['status'],
				'flag_show_in_site' => $game_type['flag_show_in_site'],
			));

			$game_type_id = $this->db->insert_id();
			foreach ($game_type['game_description_list'] as $game_description) {
				$game_description_list[] = array_merge(array(
					'game_platform_id' => OPUS_KENO_API,
					'game_type_id' => $game_type_id,
				), $game_description);
			}
		}

		$this->db->insert_batch('game_description', $game_description_list);
		$this->db->trans_complete();
	}

	public function down() {
		
		$this->db->delete('game_description', array('game_platform_id' => OPUS_KENO_API, 'game_code' => 'unknown'));
		
	}
}



