<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gameplay_rslots_in_game_type_and_game_description_201605160441 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $this->db->trans_start();

		// //insert to game_description
		// $data = array(
		// 	array(
		// 		'game_type' => 'RSlot',
		// 		'game_type_lang' => 'gameplay_rslots',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array('game_name' => 'gameplay.lanternfestival',
		// 				'english_name' => 'Lantern Festival',
		// 				'external_game_id' => 'lanternfestival',
		// 				'game_code' => 'lanternfestival',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.legendofnezha',
		// 				'english_name' => 'Legend of Nezha',
		// 				'external_game_id' => 'legendofnezha',
		// 				'game_code' => 'legendofnezha',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.deepblue',
		// 				'english_name' => 'Deep Blue',
		// 				'external_game_id' => 'deepblue',
		// 				'game_code' => 'deepblue',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.goldeneggs',
		// 				'english_name' => 'Golden Eggs',
		// 				'external_game_id' => 'goldeneggs',
		// 				'game_code' => 'goldeneggs',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.zeus',
		// 				'english_name' => 'Zeus',
		// 				'external_game_id' => 'zeus',
		// 				'game_code' => 'zeus',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.worldofwarlords',
		// 				'english_name' => 'World of Warlords',
		// 				'external_game_id' => 'worldofwarlords',
		// 				'game_code' => 'worldofwarlords',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.pharaoh',
		// 				'english_name' => 'Pharaoh',
		// 				'external_game_id' => 'pharaoh',
		// 				'game_code' => 'pharaoh',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.qixi',
		// 				'english_name' => 'Qi Xi',
		// 				'external_game_id' => 'qixi',
		// 				'game_code' => 'qixi',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.samuraisushi',
		// 				'english_name' => 'Samurai Sushi',
		// 				'external_game_id' => 'samuraisushi',
		// 				'game_code' => 'samuraisushi',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.fortunecat',
		// 				'english_name' => 'Fortune Cat',
		// 				'external_game_id' => 'fortunecat',
		// 				'game_code' => 'fortunecat',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.dimsumlicious',
		// 				'english_name' => 'Dimsumlicious',
		// 				'external_game_id' => 'dimsumlicious',
		// 				'game_code' => 'dimsumlicious',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.godofgamblers',
		// 				'english_name' => 'God of Gamblers',
		// 				'external_game_id' => 'godofgamblers',
		// 				'game_code' => 'godofgamblers',
		// 				'html_five_enabled' => self::FLAG_TRUE,
		// 				'mobile_enabled' => self::FLAG_TRUE,
		// 			),
		// 			array('game_name' => 'gameplay.sevenwonders',
		// 				'english_name' => '7 Wonders',
		// 				'external_game_id' => 'sevenwonders',
		// 				'game_code' => 'sevenwonders',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.tokyohunter',
		// 				'english_name' => 'Tokyo Hunter',
		// 				'external_game_id' => 'tokyohunter',
		// 				'game_code' => 'tokyohunter',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.fortunekoi',
		// 				'english_name' => 'Fortune Koi',
		// 				'external_game_id' => 'fortunekoi',
		// 				'game_code' => 'fortunekoi',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 			array('game_name' => 'gameplay.fortunetree',
		// 				'english_name' => 'Fortune Tree',
		// 				'external_game_id' => 'fortunetree',
		// 				'game_code' => 'fortunetree',
		// 				'html_five_enabled' => self::FLAG_FALSE,
		// 				'mobile_enabled' => self::FLAG_FALSE,
		// 			),
		// 		),
		// 	),
		// );

		// $game_description_list = array();
		// foreach ($data as $game_type) {
		// 	$this->db->insert('game_type', array(
		// 		'game_platform_id' => GAMEPLAY_API,
		// 		'game_type' => $game_type['game_type'],
		// 		'game_type_lang' => $game_type['game_type_lang'],
		// 		'status' => $game_type['status'],
		// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 	));

		// 	$game_type_id = $this->db->insert_id();
		// 	foreach ($game_type['game_description_list'] as $game_description) {
		// 		$game_description_list[] = array_merge(array(
		// 			'game_platform_id' => GAMEPLAY_API,
		// 			'game_type_id' => $game_type_id,
		// 		), $game_description);
		// 	}
		// }

		// $this->db->insert_batch('game_description', $game_description_list);
		// $this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => GAMEPLAY_API));
		// $this->db->delete('game_description', array('game_platform_id' => GAMEPLAY_API));
		// $this->db->trans_complete();
	}
}