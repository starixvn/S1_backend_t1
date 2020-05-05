<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_added_AGIN_RED_POCKET_to_game_type_and_game_description_201601281427 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		// $this->db->trans_start();

		//    $data = array(

		//     	array(
	 //           	    'game_type' => 'Red Pocket',
		// 			'game_type_lang' => 'agin.red_pocket',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(

		// 				array('game_name' => '_json:{"1":"Red Pocket","2":"红包"}',
		// 					'english_name' => 'Red Pocket',
		// 					'external_game_id' => 'RED_POCKET',
		// 					'game_code' => 'RED_POCKET'
		// 					)
		// 				)
		// 			)

		//    );

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
		// $this->db->delete('game_type', array('game_type'=> 'Red Pocket', 'game_platform_id' => AGIN_API, 'game_type !='=> 'unknown'));
		// $this->db->delete('game_description', array('game_code'=> 'RED_POCKET', 'game_platform_id' => AGIN_API,'game_name !='=> 'agin.unknown'));
		// $this->db->trans_complete();
	}
}