<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unknown_game_type_and_game_description_for_mg_quickfire_20170704 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
	

		$this->db->trans_start();

			$data = array(
				array(
					'game_type' => 'MG QUICKFIRE UNKNOWN',
					'game_type_lang' => 'MG QUICKFIRE UNKNOWN',
					'status' => self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
					'game_description_list' => array(
						array(
							'game_name' => '_json:{"1":"MG QUICKFIRE UNKNOWN GAME","2":"MG QUICKFIRE 未知游戏"}',	
							'english_name' => 'MG QUICKFIRE UNKNOWN GAME',
							'external_game_id' => 'unknown',
							'game_code' => 'unknown'
						)
					)
				)
			);

			$game_description_list = array();
			foreach ($data as $game_type) {
				$this->db->insert('game_type', array(
					'game_platform_id' => MG_QUICKFIRE_API,
					'game_type' => $game_type['game_type'],
					'game_type_lang' => $game_type['game_type_lang'],
					'status' => $game_type['status'],
					'flag_show_in_site' => $game_type['flag_show_in_site'],
				));

				$game_type_id = $this->db->insert_id();
				foreach ($game_type['game_description_list'] as $game_description) {
					$game_description_list[] = array_merge(array(
						'game_platform_id' => MG_QUICKFIRE_API,
						'game_type_id' => $game_type_id,
					), $game_description);
				}
			}

			$this->db->insert_batch('game_description', $game_description_list);
			$this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' =>  MG_QUICKFIRE_API));
		$this->db->delete('game_description', array('game_platform_id' =>  MG_QUICKFIRE_API));
		$this->db->trans_complete();
	}
}