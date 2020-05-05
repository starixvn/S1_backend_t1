<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_fishinggame_game_type_and_game_description_201609131345 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {


		// $this->db->trans_start();

		// 	//insert to game_description
		// 	$data = array(
		// 		array(
		// 			'game_type' => 'Fishing Game',
		// 			'game_type_lang' => 'fishing_game',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 					//Video Slot
		// 					array('game_name' => 'Fishing Game 捕鱼天下',
		// 					'english_name' => 'Fishing Game 捕鱼天下',
		// 					'external_game_id' => '101',
		// 					'game_code' => '101'
		// 					),
		// 					array('game_name' => 'Fishing Game 水果机',
		// 					'english_name' => 'Fishing Game 水果机',
		// 					'external_game_id' => '102',
		// 					'game_code' => '102'
		// 					),
		// 					array('game_name' => 'Fishing Game 单挑王',
		// 					'english_name' => 'Fishing Game 单挑王',
		// 					'external_game_id' => '103',
		// 					'game_code' => '103'
		// 					),
		// 					array('game_name' => 'Fishing Game 金鲨银鲨',
		// 					'english_name' => 'Fishing Game 金鲨银鲨',
		// 					'external_game_id' => '104',
		// 					'game_code' => '104'
		// 					),
		// 				),
		// 			),

		// 		);



		// 	$game_description_list = array();
		// 	foreach ($data as $game_type) {
		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => FISHINGGAME_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 		$game_type_id = $this->db->insert_id();
		// 		foreach ($game_type['game_description_list'] as $game_description) {
		// 			$game_description_list[] = array_merge(array(
		// 				'game_platform_id' => FISHINGGAME_API,
		// 				'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 		}
		// 	}

		// 	$this->db->insert_batch('game_description', $game_description_list);
		// 	$this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' =>  FISHINGGAME_API, 'game_type_lang' => 'fishing_game'));
		// $this->db->delete('game_description', array('game_platform_id' =>  FISHINGGAME_API, 'game_code' => '101'));
		// $this->db->delete('game_description', array('game_platform_id' =>  FISHINGGAME_API, 'game_code' => '102'));
		// $this->db->delete('game_description', array('game_platform_id' =>  FISHINGGAME_API, 'game_code' => '103'));
		// $this->db->delete('game_description', array('game_platform_id' =>  FISHINGGAME_API, 'game_code' => '104'));

		// $this->db->trans_complete();
	}
}