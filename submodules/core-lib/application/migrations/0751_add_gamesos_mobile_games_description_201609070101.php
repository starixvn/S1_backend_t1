<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gamesos_mobile_games_description_201609070101 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$this->db->trans_start();


		// $data = array(
		// 	array(
		// 		'game_type' => 'Card Games',
		// 		'game_type_lang' => 'gamesos_mobile_card_games',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array('game_name' => 'Baccarat',
		// 				'english_name' => 'Baccarat',
		// 				'external_game_id' => 'xc_mobilebaccarat',
		// 				'game_code' => 'xc_mobilebaccarat'
		// 				),
		// 			array('game_name' => 'Blackjack',
		// 				'english_name' => 'Blackjack',
		// 				'external_game_id' => 'xc_mobileblackjack',
		// 				'game_code' => 'xc_mobileblackjack'
		// 				),
		// 			array('game_name' => 'Blackjack Perfect Pairs',
		// 				'english_name' => 'Blackjack Perfect Pairs',
		// 				'external_game_id' => 'xc_mobilebj_perfectpairs',
		// 				'game_code' => 'xc_mobilebj_perfectpairs'
		// 				),
		// 			array('game_name' => 'Blackjack Pontoon',
		// 				'english_name' => 'Blackjack Pontoon',
		// 				'external_game_id' => 'xc_mobileblackjack_pontoon',
		// 				'game_code' => 'xc_mobileblackjack_pontoon'
		// 				),
		// 			array('game_name' => 'Blackjack Spanish',
		// 				'english_name' => 'Blackjack Spanish',
		// 				'external_game_id' => 'xc_mobileblackjack_spanish',
		// 				'game_code' => 'xc_mobileblackjack_spanish'
		// 				),
		// 			array('game_name' => 'Blackjack Surrender',
		// 				'english_name' => 'Blackjack Surrender',
		// 				'external_game_id' => 'xc_mobileblackjack_s',
		// 				'game_code' => 'xc_mobileblackjack_s'
		// 				),
		// 			array('game_name' => 'Casino Hold\'em',
		// 				'english_name' => 'Casino Hold\'em',
		// 				'external_game_id' => 'xc_mobilehold_em',
		// 				'game_code' => 'xc_mobilehold_em'
		// 				),
		// 			array('game_name' => 'Face Up 21 Blackjack',
		// 				'english_name' => 'Face Up 21 Blackjack',
		// 				'external_game_id' => 'xc_mobileblackjack_faceup',
		// 				'game_code' => 'xc_mobileblackjack_faceup'
		// 				),
		// 			array('game_name' => 'VIP Multihand Baccarat',
		// 				'english_name' => 'VIP Multihand Baccarat',
		// 				'external_game_id' => 'xc_mobilemhbaccarat',
		// 				'game_code' => 'xc_mobilemhbaccarat'
		// 				),
		// 			),
		// 		),
		// 		array(
		// 			'game_type' => 'Slots',
		// 			'game_type_lang' => 'gamesos_mobile_slots',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array('game_name' => 'Atlantis Dive Slots',
		// 					'english_name' => 'Atlantis Dive Slots',
		// 					'external_game_id' => 'xc_mobileatlantisdiveslots',
		// 					'game_code' => 'xc_mobileatlantisdiveslots'
		// 					),
		// 				array('game_name' => 'China MegaWild Slots',
		// 					'english_name' => 'China MegaWild Slots',
		// 					'external_game_id' => 'xc_mobilewildchinaslots',
		// 					'game_code' => 'xc_mobilewildchinaslots'
		// 					),
		// 				array('game_name' => 'Freaky Fruits Slots',
		// 					'english_name' => 'Freaky Fruits Slots',
		// 					'external_game_id' => 'xc_mobilefreakyfruitsslots',
		// 					'game_code' => 'xc_mobilefreakyfruitsslots'
		// 					),
		// 				array('game_name' => 'House of Scare Slots',
		// 					'english_name' => 'House of Scare Slots',
		// 					'external_game_id' => 'xc_mobilehouseofscareslots',
		// 					'game_code' => 'xc_mobilehouseofscareslots'
		// 					),
		// 				array('game_name' => 'Magic Pot Slots',
		// 					'english_name' => 'Magic Pot Slots',
		// 					'external_game_id' => 'xc_mobilemagicpotslots',
		// 					'game_code' => 'xc_mobilemagicpotslots'
		// 					),
		// 				array('game_name' => 'Party Night Slots',
		// 					'english_name' => 'Party Night Slots',
		// 					'external_game_id' => 'xc_mobilepartynightslots',
		// 					'game_code' => 'xc_mobilepartynightslots'
		// 					),
		// 				array('game_name' => 'Summer Dream Slots',
		// 					'english_name' => 'Summer Dream Slots',
		// 					'external_game_id' => 'xc_mobilesummerdreamslots',
		// 					'game_code' => 'xc_mobilesummerdreamslots'
		// 					),
		// 				array('game_name' => 'Midnight Lucky Sky',
		// 					'english_name' => 'Midnight Lucky Sky',
		// 					'external_game_id' => 'xc_mobilefirecrackerslots',
		// 					'game_code' => 'xc_mobilefirecrackerslots'
		// 					),
		// 				array('game_name' => 'Karaoke Star',
		// 					'english_name' => 'Karaoke Star',
		// 					'external_game_id' => 'xc_mobilekaraokeslots',
		// 					'game_code' => 'xc_mobilekaraokeslots'
		// 					),
		// 				array('game_name' => 'Non-Stop Party',
		// 					'english_name' => 'Non-Stop Party',
		// 					'external_game_id' => 'xc_mobilenonstoppartyslots',
		// 					'game_code' => 'xc_mobilenonstoppartyslots'
		// 					),
		// 				array('game_name' => 'Double Bonus Slots',
		// 					'english_name' => 'Double Bonus Slots',
		// 					'external_game_id' => 'xc_mobiledoublebonusslots',
		// 					'game_code' => 'xc_mobiledoublebonusslots'
		// 					),
		// 				array('game_name' => 'Maya Wheel of Luck',
		// 					'english_name' => 'Maya Wheel of Luck',
		// 					'external_game_id' => 'xc_mobilemayawheelofluckslots',
		// 					'game_code' => 'xc_mobilemayawheelofluckslots'
		// 					),
		// 				),
		// 			),
		// 			array(
		// 				'game_type' => 'Table Games',
		// 				'game_type_lang' => 'gamesos_mobile_table_games',
		// 				'status' => self::FLAG_TRUE,
		// 				'flag_show_in_site' => self::FLAG_TRUE,
		// 				'game_description_list' => array(
		// 					array('game_name' => 'American Roulette',
		// 						'english_name' => 'American Roulette',
		// 						'external_game_id' => 'xc_mobileamericanroulette',
		// 						'game_code' => 'xc_mobileamericanroulette'
		// 						),
		// 					array('game_name' => 'European Roulette',
		// 						'english_name' => 'European Roulette',
		// 						'external_game_id' => 'xc_mobileeuroulette',
		// 						'game_code' => 'xc_mobileeuroulette'
		// 						),
		// 					),
		// 				),
		// 				array(
		// 					'game_type' => 'Video Poker',
		// 					'game_type_lang' => 'gamesos_mobile_video_poker',
		// 					'status' => self::FLAG_TRUE,
		// 					'flag_show_in_site' => self::FLAG_TRUE,
		// 					'game_description_list' => array(
		// 						array('game_name' => 'Deuces Wild 4 Lines',
		// 							'english_name' => 'Deuces Wild 4 Lines',
		// 							'external_game_id' => 'xc_mobiledeuceswild4l',
		// 							'game_code' => 'xc_mobiledeuceswild4l'
		// 							),
		// 						array('game_name' => 'Jacks or Better 4 Lines',
		// 							'english_name' => 'Jacks or Better 4 Lines',
		// 							'external_game_id' => 'xc_mobilejacksorbetter4l',
		// 							'game_code' => 'xc_mobilejacksorbetter4l'
		// 							),
		// 						array('game_name' => 'Joker Wild 4 Lines',
		// 							'english_name' => 'Joker Wild 4 Lines',
		// 							'external_game_id' => 'xc_mobilejokerwild4l',
		// 							'game_code' => 'xc_mobilejokerwild4l'
		// 							),
		// 						),
		// 					)
		// 				);

		// 			$game_description_list = array();
		// 			foreach ($data as $game_type) {

		// 				$this->db->insert('game_type', array(
		// 					'game_platform_id' => GAMESOS_API,
		// 					'game_type' => $game_type['game_type'],
		// 					'game_type_lang' => $game_type['game_type_lang'],
		// 					'status' => $game_type['status'],
		// 					'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 					));

		// 				$game_type_id = $this->db->insert_id();
		// 				foreach ($game_type['game_description_list'] as $game_description) {
		// 					$game_description_list[] = array_merge(array(
		// 						'game_platform_id' => GAMESOS_API,
		// 						'game_type_id' => $game_type_id,
		// 						), $game_description);
		// 				}

		// 			}
		// 			$this->db->insert_batch('game_description', $game_description_list);


		// 			$this->db->trans_complete();

				}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => GAMESOS_API));
	// $this->db->delete('game_description', array('game_platform_id' => GAMESOS_API));
	// $this->db->trans_complete();
}
}