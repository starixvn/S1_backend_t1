<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_hb88_game_type_20160415 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $data =array(
	 //                array(
	 //                        'game_type' 			=> 'Video Slots',
	 //                        'game_type_lang' 		=> 'GAMETYPE_VIDEOSLOTS',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
		// 			array(

	 //                        'game_type' => 'Baccarat',
	 //                        'game_type_lang'  => 'GAMETYPE_BACCARAT',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
	 //                array(
	 //                        'game_type' => 'Blackjack',
	 //                        'game_type_lang'  => 'GAMETYPE_BLACKJACK',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
	 //                array(
	 //                        'game_type' => 'Casino Poker',
	 //                        'game_type_lang'  => 'GAMETYPE_CASINOPOKER',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
	 //                array(
	 //                        'game_type' => 'Classic Slots',
	 //                        'game_type_lang'  => 'GAMETYPE_CLASSICSLOTS',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
		// 			array(
	 //                        'game_type' => 'Gamble',
	 //                        'game_type_lang'  => 'GAMETYPE_GAMBLE',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
		// 			array(
	 //                        'game_type' => 'Roulette',
	 //                        'game_type_lang'  => 'GAMETYPE_ROULETTE',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
		// 			array(
	 //                        'game_type' => 'Sic Bo',
	 //                        'game_type_lang'  => 'GAMETYPE_SICBO',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    ),
		// 			array(
	 //                        'game_type' => 'Video Poker',
	 //                        'game_type_lang'  => 'GAMETYPE_VIDEOPOKER',
	 //                        'status'				=> self::FLAG_TRUE,
	 //                        'flag_show_in_site' 	=> self::FLAG_TRUE,
	 //                    )
		// 		);
		// $game_description_list = array();
		// foreach ($data as $game_type) {
		// 	$game_type['game_platform_id'] = HB_API;
		// 	$this->db->insert('game_type', $game_type);
		// 	$game_type_id = $this->db->insert_id();
		// }
	}
	public function down() {
		// $game_platform_id = HB_API;

		// $this->db->where('game_platform_id', $game_platform_id);
		// $this->db->delete('game_type');
	}
}