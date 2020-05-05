<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_flowgaming_to_game_type_and_game_description_201607020706 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $this->db->trans_start();
		// $data = array(
		// 				array(
		// 					'game_type' => 'PlayNGO Video Poker Mobile Games',
		// 					'game_type_lang' => 'fg_playngo_video_poker_mobile_games',
		// 					'status' => self::FLAG_TRUE,
		// 					'flag_show_in_site' => self::FLAG_TRUE,
		// 					'game_description_list' => array(
		// 						array(
		// 							'game_name' => 'Deuces & Joker',
		// 							'english_name' => 'Deuces & Joker',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100275',
		// 							'external_game_id' => '100275',
		// 							'related_game_desc_id' => 'deucesandjokermobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Deuces Wild MH Mobile',
		// 							'english_name' => 'Deuces Wild MH Mobile',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100270',
		// 							'external_game_id' => '100270',
		// 							'related_game_desc_id' => 'deuceswildmobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Double Bonus',
		// 							'english_name' => 'Double Bonus',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100274',
		// 							'external_game_id' => '100274',
		// 							'related_game_desc_id' => 'doublebonusmobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Jackpot Poker Mobile',
		// 							'english_name' => 'Jackpot Poker Mobile',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100276',
		// 							'external_game_id' => '100276',
		// 							'related_game_desc_id' => 'jackpotpokermobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Jacks or Better MH Mobile',
		// 							'english_name' => 'Jacks or Better MH Mobile',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100269',
		// 							'external_game_id' => '100269',
		// 							'related_game_desc_id' => 'jacksorbettermobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Joker Poker MH Mobile',
		// 							'english_name' => 'Joker Poker MH Mobile',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100271',
		// 							'external_game_id' => '100271',
		// 							'related_game_desc_id' => 'jokerpokermobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Kings or Better',
		// 							'english_name' => 'Kings or Better',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100272',
		// 							'external_game_id' => '100272',
		// 							'related_game_desc_id' => 'kingsorbettermobile',
		// 						),
		// 						array(
		// 							'game_name' => 'Tens or Better',
		// 							'english_name' => 'Tens or Better',
		// 							'html_five_enabled' =>self::FLAG_FALSE,
		// 							'flash_enabled' =>self::FLAG_FALSE,
		// 							'mobile_enabled' =>self::FLAG_TRUE,
		// 							'game_code' => '100273',
		// 							'external_game_id' => '100273',
		// 							'related_game_desc_id' => 'tensorbettermobile',
		// 						),
		// 					),
		// 				),
		// 			);

		// $game_description_list = array();
		// foreach ($data as $game_type) {

		// 	$this->db->insert('game_type', array(
		// 		'game_platform_id' => FG_API,
		// 		'game_type' => $game_type['game_type'],
		// 		'game_type_lang' => $game_type['game_type_lang'],
		// 		'status' => $game_type['status'],
		// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 	$game_type_id = $this->db->insert_id();
		// 	foreach ($game_type['game_description_list'] as $game_description) {
		// 		$game_description_list[] = array_merge(array(
		// 			'game_platform_id' => FG_API,
		// 			'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 	}

		// }

		// $this->db->insert_batch('game_description', $game_description_list);
		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => FG_API));
		// $this->db->delete('game_description', array('game_platform_id' => FG_API));
		// $this->db->trans_complete();
	}
}