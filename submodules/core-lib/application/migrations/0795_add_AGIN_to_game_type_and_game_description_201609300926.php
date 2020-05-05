<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_AGIN_to_game_type_and_game_description_201609300926 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(


// 			array('game_type' => 'EBR',
// 				'game_type_lang' => 'agin_egame',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' =>'agin.sl1',
// 						'english_name' => 'FIFA',
// 						'external_game_id' => 'SL1',
// 						'game_code' => 'SL1'
// 						),
// 					array('game_name' =>'agin.pk_j',
// 						'english_name' => 'Video Poker(jacks or better)',
// 						'external_game_id' => 'PK_J',
// 						'game_code' => 'PK_J'
// 						),
// 					array('game_name' =>'agin.sl2',
// 						'english_name' => 'Fruit Shop Frenzy',
// 						'external_game_id' => 'SL2',
// 						'game_code' => 'SL2'
// 						),
// 					array('game_name' =>'agin.sl3',
// 						'english_name' => '3D Aquarlum',
// 						'external_game_id' => 'SL3',
// 						'game_code' => 'SL3'
// 						),
// 					array('game_name' =>'agin.sl4',
// 						'english_name' => 'Speed Racing',
// 						'external_game_id' => 'SL4',
// 						'game_code' => 'SL4'
// 						),
// 					array('game_name' =>'agin.pkbj',
// 						'english_name' => 'Video poker2 (jacks or better)',
// 						'external_game_id' => 'PKBJ',
// 						'game_code' => 'PKBJ'
// 						),
// 					array('game_name' =>'agin.fru',
// 						'english_name' => 'Fruit',
// 						'external_game_id' => 'FRU',
// 						'game_code' => 'FRU'
// 						),
// 					array('game_name' =>'agin.hunter',
// 						'english_name' => 'Fish Hunter',
// 						'external_game_id' => 'hunter',
// 						'game_code' => 'hunter'
// 						),
// 					array('game_name' =>'agin.slm1',
// 						'english_name' => 'Beauty And Volleyball',
// 						'external_game_id' => 'slm1',
// 						'game_code' => 'slm1'
// 						),
// 					array('game_name' =>'agin.slm2',
// 						'english_name' => 'Fortune lamb',
// 						'external_game_id' => 'slm2',
// 						'game_code' => 'slm2'
// 						),
// 					array('game_name' =>'agin.slm3',
// 						'english_name' => 'The God of Wushu',
// 						'external_game_id' => 'slm3',
// 						'game_code' => 'slm3'
// 						),
// 					array('game_name' =>'agin.sc01',
// 						'english_name' => 'Luky Slot',
// 						'external_game_id' => 'sc01',
// 						'game_code' => 'sc01'
// 						),
// 					array('game_name' =>'agin.tglw',
// 						'english_name' => 'Speed Wheel',
// 						'external_game_id' => 'tglw',
// 						'game_code' => 'tglw'
// 						),
// 					array('game_name' =>'agin.slm4',
// 						'english_name' => 'Wu Zetian',
// 						'external_game_id' => 'slm4',
// 						'game_code' => 'slm4'
// 						),
// 					array('game_name' =>'agin.tgcw',
// 						'english_name' => 'Casino War',
// 						'external_game_id' => 'tgcw',
// 						'game_code' => 'tgcw'
// 						),
// 					array('game_name' =>'agin.sb01',
// 						'english_name' => 'Space Walker',
// 						'external_game_id' => 'sb01',
// 						'game_code' => 'sb01'
// 						),
// 					array('game_name' =>'agin.sb02',
// 						'english_name' => 'Vintage Garden',
// 						'external_game_id' => 'sb02',
// 						'game_code' => 'sb02'
// 						),
// 					array('game_name' =>'agin.sb03',
// 						'english_name' => 'Kanto cooking',
// 						'external_game_id' => 'sb03',
// 						'game_code' => 'sb03'
// 						),
// 					array('game_name' =>'agin.sb04',
// 						'english_name' => 'Ranch Coffee',
// 						'external_game_id' => 'sb04',
// 						'game_code' => 'sb04'
// 						),
// 					array('game_name' =>'agin.sb05',
// 						'english_name' => 'Sweet House',
// 						'external_game_id' => 'sb05',
// 						'game_code' => 'sb05'
// 						),
// 					array('game_name' =>'agin.sb06',
// 						'english_name' => 'Samurai',
// 						'external_game_id' => 'sb06',
// 						'game_code' => 'sb06'
// 						),
// 					),
// 				),
// 			array('game_type' => 'BR',
// 				'game_type_lang' => 'agin_live',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' =>'agin.bac',
// 						'english_name' => 'Baccarat',
// 						'external_game_id' => 'BAC',
// 						'game_code' => 'BAC'
// 						),
// 					array('game_name' =>'agin.cbac',
// 						'english_name' => 'VIP Baccarat',
// 						'external_game_id' => 'CBAC',
// 						'game_code' => 'CBAC'
// 						),
// 					array('game_name' =>'agin.link',
// 						'english_name' => 'VIP Baccarat',
// 						'external_game_id' => 'LINK',
// 						'game_code' => 'LINK'
// 						),
// 					array('game_name' =>'agin.dt',
// 						'english_name' => 'Dragon Tiger',
// 						'external_game_id' => 'DT',
// 						'game_code' => 'DT'
// 						),
// 					array('game_name' =>'agin.shb',
// 						'english_name' => 'Sicbo',
// 						'external_game_id' => 'SHB',
// 						'game_code' => 'SHB'
// 						),
// 					array('game_name' =>'agin.rou',
// 						'english_name' => 'Roulette',
// 						'external_game_id' => 'ROU',
// 						'game_code' => 'ROU'
// 						),
// 					array('game_name' =>'agin.ft',
// 						'english_name' => 'Fan Tan',
// 						'external_game_id' => 'FT',
// 						'game_code' => 'FT'
// 						),
// 					array('game_name' =>'agin.lbac',
// 						'english_name' => 'Bid Baccarat',
// 						'external_game_id' => 'LBAC',
// 						'game_code' => 'LBAC'
// 						),
// 					),
// 				),


// 		);//data

// $game_description_list = array();
// foreach ($data as $game_type) {

// 	$this->db->insert('game_type', array(
// 		'game_platform_id' => AGIN_API,
// 		'game_type' => $game_type['game_type'],
// 		'game_type_lang' => $game_type['game_type_lang'],
// 		'status' => $game_type['status'],
// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
// 		));

// 	$game_type_id = $this->db->insert_id();
// 	foreach ($game_type['game_description_list'] as $game_description) {
// 		$game_description_list[] = array_merge(array(
// 			'game_platform_id' => AGIN_API,
// 			'game_type_id' => $game_type_id,
// 			), $game_description);
// 	}

// }
// $this->db->insert_batch('game_description', $game_description_list);
// $this->db->trans_complete();

}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => AGIN_API, 'game_type !='=> 'unknown'));
	// $this->db->delete('game_description', array('game_platform_id' => AGIN_API,'game_name !='=> 'agin.unknown'));
	// $this->db->trans_complete();
}
}