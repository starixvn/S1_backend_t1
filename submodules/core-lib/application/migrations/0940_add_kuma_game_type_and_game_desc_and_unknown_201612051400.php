<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_kuma_game_type_and_game_desc_and_unknown_201612051400 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {


		// $this->db->trans_start();

		// 	//insert to game_description
		// 	$data = array(
		// 		array(
		// 			'game_type' => 'KUMA SLOTS',
		// 			'game_type_lang' => '_json:{"1":"KUMA SLOTS","2":"KUMA 老虎机"}',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => '_json:{"1":"Big Tits Heaven","2":"乳姬无双"}',
		// 					'english_name' => 'Big Tits Heaven',
		// 					'external_game_id' => '1011',
		// 					'game_code' => '1011'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Sexy Clinic","2":"性爱诊疗室"}',
		// 					'english_name' => 'Sexy Clinic',
		// 					'external_game_id' => '1012',
		// 					'game_code' => '1012'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Filthy Academic","2":"淫乱学园"}',
		// 					'english_name' => 'Filthy Academic',
		// 					'external_game_id' => '1013',
		// 					'game_code' => '1013'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Asuka’s Orgasm Climax","2":"明日花潮吹大作战"}',
		// 					'english_name' => 'Asuka’s Orgasm Climax',
		// 					'external_game_id' => '1014',
		// 					'game_code' => '1014'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Pleasure with Yuya","2":"快感‧三上悠亚"}',
		// 					'english_name' => 'Pleasure with Yuya',
		// 					'external_game_id' => '1015',
		// 					'game_code' => '1015'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"College Students’ B/D","2":"女大生调教日记"}',
		// 					'english_name' => 'College Students’ B/D',
		// 					'external_game_id' => '1016',
		// 					'game_code' => '1016'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Unlimited Internal Cum Shot","2":"忧の无限中出约会"}',
		// 					'english_name' => 'Unlimited Internal Cum Shot',
		// 					'external_game_id' => '1017',
		// 					'game_code' => '1017'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Rion’s Incredible Breasts","2":"神之乳RION"}',
		// 					'english_name' => 'Rion’s Incredible Breasts',
		// 					'external_game_id' => '1018',
		// 					'game_code' => '1018'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Estrus Temptation","2":"旬果的发情诱惑"}',
		// 					'english_name' => 'Estrus Temptation',
		// 					'external_game_id' => '1019',
		// 					'game_code' => '1019'
		// 				),
		// 				array(
		// 					'game_name' => '_json:{"1":"Tofu beauties Sexy Imagination","2":"豆腐西施淫乱觉醒"}',
		// 					'english_name' => 'Tofu beauties Sexy Imagination',
		// 					'external_game_id' => '1020',
		// 					'game_code' => '1020'
		// 				)
		// 			)
		// 		),
		// 		array(
		// 			'game_type' => 'KUMA UNKNOWN GAME',
		// 			'game_type_lang' => '_json:{"1":"KUMA UNKNOWN GAME","2":"KUMA 不明游戏"}',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_FALSE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => '_json:{"1":"KUMA UNKNOWN GAME","2":"KUMA 不明游戏"}',
		// 					'english_name' => 'KUMA UNKNOWN GAME',
		// 					'external_game_id' => 'unknown',
		// 					'game_code' => 'unknown'
		// 				)
		// 			)
		// 		)
		// 	);
		// 	$game_description_list = array();
		// 	foreach ($data as $game_type) {
		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => KUMA_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 		$game_type_id = $this->db->insert_id();
		// 		foreach ($game_type['game_description_list'] as $game_description) {
		// 			$game_description_list[] = array_merge(array(
		// 				'game_platform_id' => KUMA_API,
		// 				'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 		}
		// 	}

		// 	$this->db->insert_batch('game_description', $game_description_list);
		// 	$this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' =>  KUMA_API));
		// $this->db->delete('game_description', array('game_platform_id' =>  KUMA_API));
		// $this->db->trans_complete();
	}
}