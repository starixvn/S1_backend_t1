<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_opus_in_game_type_and_game_description_201603100505 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	private $tableName = 'game_description';

	public function up() {
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(OPUS_API, $sys)) {
		// 	$this->db->trans_start();

		// 	//insert to game_description
		// 	$data = array(
		// 		array(
		// 			'game_type' => 'Slot',
		// 			'game_type_lang' => 'opus_slots',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array('game_name' => 'opus.games.83',
		// 					'english_name' => 'Archipelago',
		// 					'external_game_id' => '83',
		// 					'game_code' => '83',
		// 				),

		// 				array('game_name' => 'opus.games.53',
		// 					'english_name' => 'Atlantis Dive',
		// 					'external_game_id' => '53',
		// 					'game_code' => '53',
		// 				),

		// 				array('game_name' => 'opus.games.1150',
		// 					'english_name' => 'Aztec Slots',
		// 					'external_game_id' => '1150',
		// 					'game_code' => '1150',
		// 				),

		// 				array('game_name' => 'opus.games.924',
		// 					'english_name' => 'Battleground Spins',
		// 					'external_game_id' => '924',
		// 					'game_code' => '924',
		// 				),

		// 				array('game_name' => 'opus.games.59',
		// 					'english_name' => 'China MegaWild',
		// 					'external_game_id' => '59',
		// 					'game_code' => '59',
		// 				),

		// 				array('game_name' => 'opus.games.1065',
		// 					'english_name' => 'Choo-Choo Slots',
		// 					'external_game_id' => '1065',
		// 					'game_code' => '1065',
		// 				),

		// 				array('game_name' => 'opus.games.54',
		// 					'english_name' => 'Cleopatra Treasure',
		// 					'external_game_id' => '54',
		// 					'game_code' => '54',
		// 				),

		// 				array('game_name' => 'opus.games.994',
		// 					'english_name' => 'Cubana Tropicana',
		// 					'external_game_id' => '994',
		// 					'game_code' => '994',
		// 				),

		// 				array('game_name' => 'opus.games.1057',
		// 					'english_name' => 'Easter Feast Slot',
		// 					'external_game_id' => '1057',
		// 					'game_code' => '1057',
		// 				),

		// 				array('game_name' => 'opus.games.931',
		// 					'english_name' => 'Farm Slot',
		// 					'external_game_id' => '931',
		// 					'game_code' => '931',
		// 				),

		// 				array('game_name' => 'opus.games.1037',
		// 					'english_name' => 'Football Cup Slot',
		// 					'external_game_id' => '1037',
		// 					'game_code' => '1037',
		// 				),

		// 				array('game_name' => 'opus.games.928',
		// 					'english_name' => 'Freaky Bandits',
		// 					'external_game_id' => '928',
		// 					'game_code' => '928',
		// 				),

		// 				array('game_name' => 'opus.games.987',
		// 					'english_name' => 'Freaky Cars',
		// 					'external_game_id' => '987',
		// 					'game_code' => '987',
		// 				),

		// 				array('game_name' => 'opus.games.927',
		// 					'english_name' => 'Freaky Cowboys',
		// 					'external_game_id' => '927',
		// 					'game_code' => '927',
		// 				),

		// 				array('game_name' => 'opus.games.55',
		// 					'english_name' => 'Freaky Fruits',
		// 					'external_game_id' => '55',
		// 					'game_code' => '55',
		// 				),

		// 				array('game_name' => 'opus.games.922',
		// 					'english_name' => 'Freaky Gym',
		// 					'external_game_id' => '922',
		// 					'game_code' => '922',
		// 				),

		// 				array('game_name' => 'opus.games.921',
		// 					'english_name' => 'Freaky Wild West',
		// 					'external_game_id' => '921',
		// 					'game_code' => '921',
		// 				),

		// 				array('game_name' => 'opus.games.930',
		// 					'english_name' => 'Gems and the City',
		// 					'external_game_id' => '930',
		// 					'game_code' => '930',
		// 				),

		// 				array('game_name' => 'opus.games.1132',
		// 					'english_name' => 'Golden India',
		// 					'external_game_id' => '1132',
		// 					'game_code' => '1132',
		// 				),

		// 				array('game_name' => 'opus.games.82',
		// 					'english_name' => 'Hot 7\'s',
		// 					'external_game_id' => '82',
		// 					'game_code' => '82',
		// 				),

		// 				array('game_name' => 'opus.games.1039',
		// 					'english_name' => 'House of Scare Slot',
		// 					'external_game_id' => '1039',
		// 					'game_code' => '1039',
		// 				),

		// 				array('game_name' => 'opus.games.1005',
		// 					'english_name' => 'Jour de l\'Amour Slot',
		// 					'external_game_id' => '1005',
		// 					'game_code' => '1005',
		// 				),

		// 				array('game_name' => 'opus.games.1067',
		// 					'english_name' => 'Karaoke Star',
		// 					'external_game_id' => '1067',
		// 					'game_code' => '1067',
		// 				),

		// 				array('game_name' => 'opus.games.999',
		// 					'english_name' => 'Magic Pot Slot',
		// 					'external_game_id' => '999',
		// 					'game_code' => '999',
		// 				),

		// 				array('game_name' => 'opus.games.24',
		// 					'english_name' => 'Mexican Slots',
		// 					'external_game_id' => '24',
		// 					'game_code' => '24',
		// 				),

		// 				array('game_name' => 'opus.games.1169',
		// 					'english_name' => 'Midnight Lucky Sky (NEW)',
		// 					'external_game_id' => '1169',
		// 					'game_code' => '1169',
		// 				),

		// 				array('game_name' => 'opus.games.31',
		// 					'english_name' => 'Mystic Slots',
		// 					'external_game_id' => '31',
		// 					'game_code' => '31',
		// 				),

		// 				array('game_name' => 'opus.games.923',
		// 					'english_name' => 'New York Gangs',
		// 					'external_game_id' => '923',
		// 					'game_code' => '923',
		// 				),

		// 				array('game_name' => 'opus.games.1008',
		// 					'english_name' => 'Non-stop Party Slot',
		// 					'external_game_id' => '1008',
		// 					'game_code' => '1008',
		// 				),

		// 				array('game_name' => 'opus.games.20',
		// 					'english_name' => 'Olympic Slots',
		// 					'external_game_id' => '20',
		// 					'game_code' => '20',
		// 				),

		// 				array('game_name' => 'opus.games.33',
		// 					'english_name' => 'Party Night',
		// 					'external_game_id' => '33',
		// 					'game_code' => '33',
		// 				),

		// 				array('game_name' => 'opus.games.19',
		// 					'english_name' => 'Pirate Slots',
		// 					'external_game_id' => '19',
		// 					'game_code' => '19',
		// 				),

		// 				array('game_name' => 'opus.games.951',
		// 					'english_name' => 'Polar Tale',
		// 					'external_game_id' => '951',
		// 					'game_code' => '951',
		// 				),

		// 				array('game_name' => 'opus.games.1168',
		// 					'english_name' => 'Rock the mouse (NEW)',
		// 					'external_game_id' => '1168',
		// 					'game_code' => '1168',
		// 				),

		// 				array('game_name' => 'opus.games.1038',
		// 					'english_name' => 'Space Robbers Slot',
		// 					'external_game_id' => '1038',
		// 					'game_code' => '1038',
		// 				),

		// 				array(
		// 					'game_name' => 'opus.games.57',
		// 					'english_name' => 'Spin the World',
		// 					'external_game_id' => '57',
		// 					'game_code' => '57',
		// 				),

		// 				array(
		// 					'game_name' => 'opus.games.67',
		// 					'english_name' => 'Summer Dream',
		// 					'external_game_id' => '67',
		// 					'game_code' => '67',
		// 				),

		// 				array(
		// 					'game_name' => 'opus.games.84',
		// 					'english_name' => 'Totem Quest',
		// 					'external_game_id' => '84',
		// 					'game_code' => '84',
		// 				),

		// 				array('game_name' => 'opus.games.54',
		// 					'english_name' => 'Vampire Slayers',
		// 					'external_game_id' => '54',
		// 					'game_code' => '54',
		// 				),

		// 				array('game_name' => 'opus.games.925',
		// 					'english_name' => 'World-Cup Soccer Spins',
		// 					'external_game_id' => '925',
		// 					'game_code' => '925',
		// 				),

		// 				array('game_name' => 'opus.games.1196',
		// 					'english_name' => 'Double Bonus Slots',
		// 					'external_game_id' => '1196',
		// 					'game_code' => '1196',
		// 				),
		// 			),
		// 		),

		// 		array(
		// 			'game_type' => 'Table',
		// 			'game_type_lang' => 'opus_table',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => 'opus.games.74',
		// 					'english_name' => 'Craps',
		// 					'external_game_id' => '74',
		// 					'game_code' => '74',
		// 				),

		// 				array('game_name' => 'opus.games.70',
		// 					'english_name' => 'European Roulette',
		// 					'external_game_id' => '70',
		// 					'game_code' => '70',
		// 				),

		// 				array('game_name' => 'opus.games.71',
		// 					'english_name' => 'Roulette PRO',
		// 					'external_game_id' => '71',
		// 					'game_code' => '71',
		// 				),

		// 				array('game_name' => 'opus.games.69',
		// 					'english_name' => 'American Roulette',
		// 					'external_game_id' => '69',
		// 					'game_code' => '69',
		// 				),

		// 				array('game_name' => 'opus.games.79',
		// 					'english_name' => 'Sic Bo',
		// 					'external_game_id' => '79',
		// 					'game_code' => '79',
		// 				),
		// 			),
		// 		),

		// 		array(
		// 			'game_type' => 'Card',
		// 			'game_type_lang' => 'opus_card',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => 'opus.games.1036',
		// 					'english_name' => 'Spanish Blackjack',
		// 					'external_game_id' => '1036',
		// 					'game_code' => '1036',
		// 				),

		// 				array('game_name' => 'opus.games.75',
		// 					'english_name' => 'Baccarat',
		// 					'external_game_id' => '75',
		// 					'game_code' => '75',
		// 				),

		// 				array('game_name' => 'opus.games.918',
		// 					'english_name' => 'Pontoon Classic',
		// 					'external_game_id' => '918',
		// 					'game_code' => '918',
		// 				),

		// 				array('game_name' => 'opus.games.73',
		// 					'english_name' => 'Blackjack Surrender Classic',
		// 					'external_game_id' => '73',
		// 					'game_code' => '73',
		// 				),

		// 				array('game_name' => 'opus.games.111',
		// 					'english_name' => 'Casino Hold\'em',
		// 					'external_game_id' => '111',
		// 					'game_code' => '111',
		// 				),

		// 				array('game_name' => 'opus.games.946',
		// 					'english_name' => 'Oasis Poker',
		// 					'external_game_id' => '946',
		// 					'game_code' => '946',
		// 				),

		// 				array('game_name' => 'opus.games.72',
		// 					'english_name' => 'Blackjack Classic',
		// 					'external_game_id' => '72',
		// 					'game_code' => '72',
		// 				),

		// 				array('game_name' => 'opus.games.952',
		// 					'english_name' => 'Blackjack Progressive Classic',
		// 					'external_game_id' => '952',
		// 					'game_code' => '952',
		// 				),

		// 				array('game_name' => 'opus.games.920',
		// 					'english_name' => 'Blackjack Switch Classic',
		// 					'external_game_id' => '920',
		// 					'game_code' => '920',
		// 				),

		// 				array('game_name' => 'opus.games.916',
		// 					'english_name' => 'Face Up 21 Blackjack Classic',
		// 					'external_game_id' => '916',
		// 					'game_code' => '916',
		// 				),

		// 				array('game_name' => 'opus.games.947',
		// 					'english_name' => 'Russian Poker',
		// 					'external_game_id' => '947',
		// 					'game_code' => '947',
		// 				),

		// 				array('game_name' => 'opus.games.919',
		// 					'english_name' => 'Spanish Blackjack Classic',
		// 					'external_game_id' => '919',
		// 					'game_code' => '919',
		// 				),

		// 				array('game_name' => 'opus.games.80',
		// 					'english_name' => 'Pai Gow Poker',
		// 					'external_game_id' => '80',
		// 					'game_code' => '80',
		// 				),

		// 				array('game_name' => 'opus.games.917',
		// 					'english_name' => 'Perfect Pairs Blackjack Classic',
		// 					'external_game_id' => '917',
		// 					'game_code' => '917',
		// 				),

		// 				array('game_name' => 'opus.games.1003',
		// 					'english_name' => 'Blackjack',
		// 					'external_game_id' => '1003',
		// 					'game_code' => '1003',
		// 				),

		// 				array('game_name' => 'opus.games.991',
		// 					'english_name' => 'Blackjack Surrender',
		// 					'external_game_id' => '991',
		// 					'game_code' => '991',
		// 				),

		// 				array('game_name' => 'opus.games.990',
		// 					'english_name' => 'Face Up 21 Blackjack',
		// 					'external_game_id' => '990',
		// 					'game_code' => '990',
		// 				),

		// 				array('game_name' => 'opus.games.1004',
		// 					'english_name' => 'Perfect Pairs Blackjack',
		// 					'external_game_id' => '1004',
		// 					'game_code' => '1004',
		// 				),

		// 				array('game_name' => 'opus.games.1052',
		// 					'english_name' => 'Pontoon',
		// 					'external_game_id' => '1052',
		// 					'game_code' => '1052',
		// 				),

		// 				array('game_name' => 'opus.games.1009',
		// 					'english_name' => 'Blackjack Switch',
		// 					'external_game_id' => '1009',
		// 					'game_code' => '1009',
		// 				),

		// 				array('game_name' => 'opus.games.1160',
		// 					'english_name' => 'VIP Baccarat (NEW)',
		// 					'external_game_id' => '1160',
		// 					'game_code' => '1160',
		// 				),
		// 			),
		// 		),

		// 		array(
		// 			'game_type' => 'Video Poker',
		// 			'game_type_lang' => 'opus_video_poker',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => 'opus.games.90',
		// 					'english_name' => 'Deuces Wild',
		// 					'external_game_id' => '90',
		// 					'game_code' => '90',
		// 				),

		// 				array('game_name' => 'opus.games.91',
		// 					'english_name' => '4-Line Deuces Wild',
		// 					'external_game_id' => '91',
		// 					'game_code' => '91',
		// 				),

		// 				array('game_name' => 'opus.games.92',
		// 					'english_name' => '10-Line Deuces Wild',
		// 					'external_game_id' => '92',
		// 					'game_code' => '92',
		// 				),

		// 				array('game_name' => 'opus.games.93',
		// 					'english_name' => '25-Line Deuces Wild',
		// 					'external_game_id' => '93',
		// 					'game_code' => '93',
		// 				),

		// 				array('game_name' => 'opus.games.94',
		// 					'english_name' => '50-Line Deuces Wild',
		// 					'external_game_id' => '94',
		// 					'game_code' => '94',
		// 				),

		// 				array('game_name' => 'opus.games.95',
		// 					'english_name' => 'Joker Wild',
		// 					'external_game_id' => '95',
		// 					'game_code' => '95',
		// 				),

		// 				array('game_name' => 'opus.games.96',
		// 					'english_name' => '4-Line Joker Wild',
		// 					'external_game_id' => '96',
		// 					'game_code' => '96',
		// 				),

		// 				array('game_name' => 'opus.games.97',
		// 					'english_name' => '10-Line Joker Wild',
		// 					'external_game_id' => '97',
		// 					'game_code' => '97',
		// 				),

		// 				array('game_name' => 'opus.games.98',
		// 					'english_name' => '25-Line Joker Wild',
		// 					'external_game_id' => '98',
		// 					'game_code' => '98',
		// 				),

		// 				array('game_name' => 'opus.games.99',
		// 					'english_name' => '50-Line Joker Wild',
		// 					'external_game_id' => '99',
		// 					'game_code' => '99',
		// 				),

		// 				array('game_name' => 'opus.games.85',
		// 					'english_name' => 'Jacks or Better',
		// 					'external_game_id' => '85',
		// 					'game_code' => '85',
		// 				),

		// 				array('game_name' => 'opus.games.86',
		// 					'english_name' => '4-Line Jacks Or Better',
		// 					'external_game_id' => '86',
		// 					'game_code' => '86',
		// 				),

		// 				array('game_name' => 'opus.games.87',
		// 					'english_name' => '10-Line Jacks Or Better',
		// 					'external_game_id' => '87',
		// 					'game_code' => '87',
		// 				),

		// 				array('game_name' => 'opus.games.88',
		// 					'english_name' => '25-Line Jacks Or Better',
		// 					'external_game_id' => '88',
		// 					'game_code' => '88',
		// 				),

		// 				array('game_name' => 'opus.games.89',
		// 					'english_name' => '50-Line Jacks Or Better',
		// 					'external_game_id' => '89',
		// 					'game_code' => '89',
		// 				),

		// 				array('game_name' => 'opus.games.105',
		// 					'english_name' => 'Face The Ace',
		// 					'external_game_id' => '105',
		// 					'game_code' => '105',
		// 				),

		// 				array('game_name' => 'opus.games.106',
		// 					'english_name' => '4-Line Face The Ace',
		// 					'external_game_id' => '106',
		// 					'game_code' => '106',
		// 				),

		// 				array('game_name' => 'opus.games.107',
		// 					'english_name' => '10-Line FaceThe Ace',
		// 					'external_game_id' => '107',
		// 					'game_code' => '107',
		// 				),

		// 				array('game_name' => 'opus.games.108',
		// 					'english_name' => '25-Line Face The Ace',
		// 					'external_game_id' => '108',
		// 					'game_code' => '108',
		// 				),

		// 				array('game_name' => 'opus.games.109',
		// 					'english_name' => '50-Line Face The Ace',
		// 					'external_game_id' => '109',
		// 					'game_code' => '109',
		// 				),

		// 				array('game_name' => 'opus.games.110',
		// 					'english_name' => 'Shockwave Poker',
		// 					'external_game_id' => '110',
		// 					'game_code' => '110',
		// 				),
		// 			),
		// 		),

		// 		array(
		// 			'game_type' => 'Jackpot',
		// 			'game_type_lang' => 'opus_jackpot',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_TRUE,
		// 			'game_description_list' => array(
		// 				array(
		// 					'game_name' => 'opus.games.939',
		// 					'english_name' => 'Basebull Scratch',
		// 					'external_game_id' => '939',
		// 					'game_code' => '939',
		// 				),

		// 				array('game_name' => 'opus.games.78',
		// 					'english_name' => 'Bingo Scratch',
		// 					'external_game_id' => '78',
		// 					'game_code' => '78',
		// 				),

		// 				array('game_name' => 'opus.games.122',
		// 					'english_name' => 'Black and White',
		// 					'external_game_id' => '122',
		// 					'game_code' => '122',
		// 				),

		// 				array('game_name' => 'opus.games.936',
		// 					'english_name' => 'Formula Scratch',
		// 					'external_game_id' => '936',
		// 					'game_code' => '936',
		// 				),

		// 				array('game_name' => 'opus.games.941',
		// 					'english_name' => 'Freaky Thimbles',
		// 					'external_game_id' => '941',
		// 					'game_code' => '941',
		// 				),

		// 				array('game_name' => 'opus.games.933',
		// 					'english_name' => 'Soccer Scratch',
		// 					'external_game_id' => '933',
		// 					'game_code' => '933',
		// 				),

		// 				array('game_name' => 'opus.games.940',
		// 					'english_name' => 'Gangster\'s Loot',
		// 					'external_game_id' => '940',
		// 					'game_code' => '940',
		// 				),

		// 				array('game_name' => 'opus.games.938',
		// 					'english_name' => 'Gold 999.9',
		// 					'external_game_id' => '938',
		// 					'game_code' => '938',
		// 				),

		// 				array('game_name' => 'opus.games.119',
		// 					'english_name' => 'Hockey Potshot',
		// 					'external_game_id' => '119',
		// 					'game_code' => '119',
		// 				),

		// 				array('game_name' => 'opus.games.77',
		// 					'english_name' => 'Keno',
		// 					'external_game_id' => '77',
		// 					'game_code' => '77',
		// 				),

		// 				array('game_name' => 'opus.games.935',
		// 					'english_name' => 'Monsters Scratch',
		// 					'external_game_id' => '935',
		// 					'game_code' => '935',
		// 				),

		// 				array('game_name' => 'opus.games.932',
		// 					'english_name' => 'Moonapolis',
		// 					'external_game_id' => '932',
		// 					'game_code' => '932',
		// 				),

		// 				array('game_name' => 'opus.games.118',
		// 					'english_name' => 'Soccer Shot',
		// 					'external_game_id' => '118',
		// 					'game_code' => '118',
		// 				),

		// 				array('game_name' => 'opus.games.937',
		// 					'english_name' => 'Sparkle Ladies',
		// 					'external_game_id' => '937',
		// 					'game_code' => '937',
		// 				),

		// 				array('game_name' => 'opus.games.934',
		// 					'english_name' => 'Teddy Scratch',
		// 					'external_game_id' => '934',
		// 					'game_code' => '934',
		// 				),

		// 				array('game_name' => 'opus.games.998',
		// 					'english_name' => 'Magic Pot Scratch',
		// 					'external_game_id' => '998',
		// 					'game_code' => '998',
		// 				),

		// 				array('game_name' => 'opus.games.992',
		// 					'english_name' => 'Cubana-Tropicana Scratch',
		// 					'external_game_id' => '992',
		// 					'game_code' => '992',
		// 				),

		// 				array('game_name' => 'opus.games.995',
		// 					'english_name' => 'Jour de l\'Amour',
		// 					'external_game_id' => '995',
		// 					'game_code' => '995',
		// 				),

		// 				array('game_name' => 'opus.games.1006',
		// 					'english_name' => 'House of Scare Scratch',
		// 					'external_game_id' => '1006',
		// 					'game_code' => '1006',
		// 				),

		// 				array('game_name' => 'opus.games.1051',
		// 					'english_name' => 'Non-stop Party Scratch',
		// 					'external_game_id' => '1051',
		// 					'game_code' => '1051',
		// 				),

		// 				array('game_name' => 'opus.games.1007',
		// 					'english_name' => 'Football Cup Scratch',
		// 					'external_game_id' => '1007',
		// 					'game_code' => '1007',
		// 				),
		// 			),
		// 		),

		// 		array(
		// 			'game_type' => 'unknown',
		// 			'game_type_lang' => 'opus.unknown',
		// 			'status' => self::FLAG_TRUE,
		// 			'flag_show_in_site' => self::FLAG_FALSE,
		// 			'game_description_list' => array(

		// 			),
		// 		),

		// 	);

		// 	$game_description_list = array();
		// 	foreach ($data as $game_type) {
		// 		$this->db->insert('game_type', array(
		// 			'game_platform_id' => OPUS_API,
		// 			'game_type' => $game_type['game_type'],
		// 			'game_type_lang' => $game_type['game_type_lang'],
		// 			'status' => $game_type['status'],
		// 			'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 		));

		// 		$game_type_id = $this->db->insert_id();
		// 		foreach ($game_type['game_description_list'] as $game_description) {
		// 			$game_description_list[] = array_merge(array(
		// 				'game_platform_id' => OPUS_API,
		// 				'game_type_id' => $game_type_id,
		// 			), $game_description);
		// 		}
		// 	}

		// 	$this->db->insert_batch('game_description', $game_description_list);
		// 	$this->db->trans_complete();
		// }
	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => OPUS_API));
		// $this->db->delete('game_description', array('game_platform_id' => OPUS_API));
		// $this->db->trans_complete();
	}
}