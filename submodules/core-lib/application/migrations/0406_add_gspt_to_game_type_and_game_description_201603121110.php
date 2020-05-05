<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gspt_to_game_type_and_game_description_201603121110 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(
// 			array(
// 				'game_type' => 'Card Games',
// 				'game_type_lang' => 'gspt_card_games',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array(
// 						'game_name' => 'gspt.21 Duel Blackjack',
// 						'game_code' => 'bj21d_mh',
// 						'english_name' => '21 Duel Blackjack',
// 						'external_game_id' => 'bj21d_mh'
// 						),
// 					array(
// 						'game_name' => 'gspt.3 Card Brag',
// 						'game_code' => 'ash3brg',
// 						'english_name' => '3 Card Brag',
// 						'external_game_id' => 'ash3brg'
// 						),
// 					array(
// 						'game_name' => 'gspt.American Blackjack',
// 						'game_code' => 'bja',
// 						'english_name' => 'American Blackjack',
// 						'external_game_id' => 'bja'
// 						),
// 					array(
// 						'game_name' => 'gspt.Baccarat',
// 						'game_code' => 'ba',
// 						'english_name' => 'Baccarat',
// 						'external_game_id' => 'ba'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack',
// 						'game_code' => 'bj_mh5',
// 						'english_name' => 'Blackjack',
// 						'external_game_id' => 'bj_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack Pro',
// 						'game_code' => 'psdbj',
// 						'english_name' => 'Blackjack Pro',
// 						'external_game_id' => 'psdbj'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack Super 21',
// 						'game_code' => 's21',
// 						'english_name' => 'Blackjack Super 21',
// 						'external_game_id' => 's21'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack Surrender',
// 						'game_code' => 'bjsd_mh5',
// 						'english_name' => 'Blackjack Surrender',
// 						'external_game_id' => 'bjsd_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack Switch',
// 						'game_code' => 'bjs',
// 						'english_name' => 'Blackjack Switch',
// 						'external_game_id' => 'bjs'
// 						),
// 					array(
// 						'game_name' => 'gspt.Blackjack UK',
// 						'game_code' => 'bjuk_mh5',
// 						'english_name' => 'Blackjack UK',
// 						'external_game_id' => 'bjuk_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Caribbean Stud® Poker',
// 						'game_code' => 'car',
// 						'english_name' => 'Caribbean Stud® Poker',
// 						'external_game_id' => 'car'
// 						),
// 					array(
// 						'game_name' => 'gspt.Casino Hold \'Em',
// 						'game_code' => 'cheaa',
// 						'english_name' => 'Casino Hold \'Em',
// 						'external_game_id' => 'cheaa'
// 						),
// 					array(
// 						'game_name' => 'gspt.Half Double Blackjack',
// 						'game_code' => 'bjhd_5',
// 						'english_name' => 'Half Double Blackjack',
// 						'external_game_id' => 'bjhd_5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Lucky Blackjack',
// 						'game_code' => 'lbj',
// 						'english_name' => 'Lucky Blackjack',
// 						'external_game_id' => 'lbj'
// 						),
// 					array(
// 						'game_name' => 'gspt.Pai Gow Poker',
// 						'game_code' => 'pg',
// 						'english_name' => 'Pai Gow Poker',
// 						'external_game_id' => 'pg'
// 						),
// 					array(
// 						'game_name' => 'gspt.Perfect Blackjack',
// 						'game_code' => 'pfbj_mh5',
// 						'english_name' => 'Perfect Blackjack',
// 						'external_game_id' => 'pfbj_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Pontoon',
// 						'game_code' => 'pon_mh5',
// 						'english_name' => 'Pontoon',
// 						'external_game_id' => 'pon_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Progressive Baccarat',
// 						'game_code' => 'pba',
// 						'english_name' => 'Progressive Baccarat',
// 						'external_game_id' => 'pba'
// 						),
// 					array(
// 						'game_name' => 'gspt.Progressive Blackjack™',
// 						'game_code' => 'pbj',
// 						'english_name' => 'Progressive Blackjack™',
// 						'external_game_id' => 'pbj'
// 						),
// 					array(
// 						'game_name' => 'gspt.Progressive Blackjack™ Multihand',
// 						'game_code' => 'pbj_mh5',
// 						'english_name' => 'Progressive Blackjack™ Multihand',
// 						'external_game_id' => 'pbj_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Red Dog',
// 						'game_code' => 'rd',
// 						'english_name' => 'Red Dog',
// 						'external_game_id' => 'rd'
// 						),
// 					array(
// 						'game_name' => 'gspt.Six-Deck Blackjack Peek',
// 						'game_code' => 'p6dbj_mh5',
// 						'english_name' => 'Six-Deck Blackjack Peek',
// 						'external_game_id' => 'p6dbj_mh5'
// 						),
// 					array(
// 						'game_name' => 'gspt.Stravaganza',
// 						'game_code' => 'str',
// 						'english_name' => 'Stravaganza',
// 						'external_game_id' => 'str'
// 						),
// 					array(
// 						'game_name' => 'gspt.Tequila Poker',
// 						'game_code' => 'tqp',
// 						'english_name' => 'Tequila Poker',
// 						'external_game_id' => 'tqp'
// 						),
// 					array(
// 						'game_name' => 'gspt.Wild Viking',
// 						'game_code' => 'wv',
// 						'english_name' => 'Wild Viking',
// 						'external_game_id' => 'wv'
// 						),
// 					array(
// 						'game_name' => 'gspt.8-Ball Slots',
// 						'game_code' => '8bs',
// 						'english_name' => '8-Ball Slots',
// 						'external_game_id' => '8bs'
// 						),
// 					array(
// 						'game_name' => 'gspt.Alchemist’s Lab',
// 						'game_code' => 'al',
// 						'english_name' => 'Alchemist’s Lab',
// 						'external_game_id' => 'al'
// 						),
// 					array(
// 						'game_name' => 'gspt.Bermuda Triangle',
// 						'game_code' => 'bt',
// 						'english_name' => 'Bermuda Triangle',
// 						'external_game_id' => 'bt'
// 						),
// 					array(
// 						'game_name' => 'gspt.Crazy 7',
// 						'game_code' => 'c7',
// 						'english_name' => 'Crazy 7',
// 						'external_game_id' => 'c7'
// 						),
// 					array(
// 						'game_name' => 'gspt.Fountain of Youth',
// 						'game_code' => 'foy',
// 						'english_name' => 'Fountain of Youth',
// 						'external_game_id' => 'foy'
// 						),
// 					array(
// 						'game_name' => 'gspt.Funky Monkey',
// 						'game_code' => 'fm',
// 						'english_name' => 'Funky Monkey',
// 						'external_game_id' => 'fm'
// 						),
// 					array(
// 						'game_name' => 'gspt.Haunted House',
// 						'game_code' => 'hh',
// 						'english_name' => 'Haunted House',
// 						'external_game_id' => 'hh'
// 						),
// 					array(
// 						'game_name' => 'gspt.Jungle Boogie',
// 						'game_code' => 'jb',
// 						'english_name' => 'Jungle Boogie',
// 						'external_game_id' => 'jb'
// 						),
// 					array(
// 						'game_name' => 'gspt.Magic Slots',
// 						'game_code' => 'ms',
// 						'english_name' => 'Magic Slots',
// 						'external_game_id' => 'ms'
// 						),
// 					array(
// 						'game_name' => 'gspt.Neptune’s Kingdom',
// 						'game_code' => 'nk',
// 						'english_name' => 'Neptune’s Kingdom',
// 						'external_game_id' => 'nk'
// 						),
// 					array(
// 						'game_name' => 'gspt.Party Line',
// 						'game_code' => 'pl',
// 						'english_name' => 'Party Line',
// 						'external_game_id' => 'pl'
// 						),
// 					array(
// 						'game_name' => 'gspt.Reel Classic 3',
// 						'game_code' => 'ssl',
// 						'english_name' => 'Reel Classic 3',
// 						'external_game_id' => 'ssl'
// 						),
// 					array(
// 						'game_name' => 'gspt.Reel Classic 5',
// 						'game_code' => 'sfr',
// 						'english_name' => 'Reel Classic 5',
// 						'external_game_id' => 'sfr'
// 						),
// 					array(
// 						'game_name' => 'gspt.Rock ‘n’ Roller',
// 						'game_code' => 'rnr',
// 						'english_name' => 'Rock ‘n’ Roller',
// 						'external_game_id' => 'rnr'
// 						),
// 					array(
// 						'game_name' => 'gspt.Safecracker',
// 						'game_code' => 'sc',
// 						'english_name' => 'Safecracker',
// 						'external_game_id' => 'sc'
// 						),
// 					array(
// 						'game_name' => 'gspt.Sultan’s Fortune',
// 						'game_code' => 'sf',
// 						'english_name' => 'Sultan’s Fortune',
// 						'external_game_id' => 'sf'
// 						),
// 					array(
// 						'game_name' => 'gspt.Tres Amigos',
// 						'game_code' => 'ta',
// 						'english_name' => 'Tres Amigos',
// 						'external_game_id' => 'ta'
// 						)
// 					),
// ),
// array(
// 	'game_type' => 'Fixed-Odds Games',
// 	'game_type_lang' => 'gspt_fixed_odds_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 		array(
// 			'game_name' => 'gspt.Around the World',
// 			'game_code' => 'atw',
// 			'english_name' => 'Around the World',
// 			'external_game_id' => 'atw'
// 			),

// 		array(
// 			'game_name' => 'gspt.Bonus Bowling',
// 			'game_code' => 'bowl',
// 			'english_name' => 'Bonus Bowling',
// 			'external_game_id' => 'bowl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cashblox',
// 			'game_code' => 'gtscb',
// 			'english_name' => 'Cashblox',
// 			'external_game_id' => 'gtscb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Darts',
// 			'game_code' => 'qbd',
// 			'english_name' => 'Darts',
// 			'external_game_id' => 'qbd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Darts 180',
// 			'game_code' => 'drts',
// 			'english_name' => 'Darts 180',
// 			'external_game_id' => 'drts'
// 			),
// 		array(
// 			'game_name' => 'gspt.Derby Day (former Horse Racing)',
// 			'game_code' => 'hr',
// 			'english_name' => 'Derby Day (former Horse Racing)',
// 			'external_game_id' => 'hr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dice Twister',
// 			'game_code' => 'dctw',
// 			'english_name' => 'Dice Twister',
// 			'external_game_id' => 'dctw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Final Score',
// 			'game_code' => 'fsc',
// 			'english_name' => 'Final Score',
// 			'external_game_id' => 'fsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fixed-Odds Slots',
// 			'game_code' => 'fosl',
// 			'english_name' => 'Fixed-Odds Slots',
// 			'external_game_id' => 'fosl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fortune Keno',
// 			'game_code' => 'kf',
// 			'english_name' => 'Fortune Keno',
// 			'external_game_id' => 'kf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Frankie’s Fantastic 7',
// 			'game_code' => 'tps',
// 			'english_name' => 'Frankie’s Fantastic 7',
// 			'external_game_id' => 'tps'
// 			),
// 		array(
// 			'game_name' => 'gspt.Genie\'s Hi-Lo',
// 			'game_code' => 'ghl',
// 			'english_name' => 'Genie\'s Hi-Lo',
// 			'external_game_id' => 'ghl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Genie\'s Hi-Lo Progressive',
// 			'game_code' => 'ghlj',
// 			'english_name' => 'Genie\'s Hi-Lo Progressive',
// 			'external_game_id' => 'ghlj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Heads or Tails',
// 			'game_code' => 'head',
// 			'english_name' => 'Heads or Tails',
// 			'external_game_id' => 'head'
// 			),
// 		array(
// 			'game_name' => 'gspt.Hold\'em Showdown',
// 			'game_code' => 'hsd',
// 			'english_name' => 'Hold\'em Showdown',
// 			'external_game_id' => 'hsd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Keno',
// 			'game_code' => 'kn',
// 			'english_name' => 'Keno',
// 			'external_game_id' => 'kn'
// 			),
// 		array(
// 			'game_name' => 'gspt.Keno Xperiment',
// 			'game_code' => 'knx',
// 			'english_name' => 'Keno Xperiment',
// 			'external_game_id' => 'knx'
// 			),
// 		array(
// 			'game_name' => 'gspt.King Derby',
// 			'game_code' => 'kgdb',
// 			'english_name' => 'King Derby',
// 			'external_game_id' => 'kgdb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Knockout',
// 			'game_code' => 'gog',
// 			'english_name' => 'Knockout',
// 			'external_game_id' => 'gog'
// 			),
// 		array(
// 			'game_name' => 'gspt.Medusa’s Gaze',
// 			'game_code' => 'gts35',
// 			'english_name' => 'Medusa’s Gaze',
// 			'external_game_id' => 'gts35'
// 			),
// 		array(
// 			'game_name' => 'gspt.Mega Ball',
// 			'game_code' => 'bls',
// 			'english_name' => 'Mega Ball',
// 			'external_game_id' => 'bls'
// 			),
// 		array(
// 			'game_name' => 'gspt.Mini Roulette',
// 			'game_code' => 'mro',
// 			'english_name' => 'Mini Roulette',
// 			'external_game_id' => 'mro'
// 			),
// 		array(
// 			'game_name' => 'gspt.Monkey Thunderbolt',
// 			'game_code' => 'mnkt',
// 			'english_name' => 'Monkey Thunderbolt',
// 			'external_game_id' => 'mnkt'
// 			),
// 		array(
// 			'game_name' => 'gspt.Penalty Shootout',
// 			'game_code' => 'pso',
// 			'english_name' => 'Penalty Shootout',
// 			'external_game_id' => 'pso'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pinball Roulette',
// 			'game_code' => 'pbro',
// 			'english_name' => 'Pinball Roulette',
// 			'external_game_id' => 'pbro'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pop Bingo',
// 			'game_code' => 'pop',
// 			'english_name' => 'Pop Bingo',
// 			'external_game_id' => 'pop'
// 			),
// 		array(
// 			'game_name' => 'gspt.Rock-Paper-Scissors',
// 			'game_code' => 'rps',
// 			'english_name' => 'Rock-Paper-Scissors',
// 			'external_game_id' => 'rps'
// 			),
// 		array(
// 			'game_name' => 'gspt.Roller Coaster Dice',
// 			'game_code' => 'rcd',
// 			'english_name' => 'Roller Coaster Dice',
// 			'external_game_id' => 'rcd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Rubik’s Riches',
// 			'game_code' => 'gtsru',
// 			'english_name' => 'Rubik’s Riches',
// 			'external_game_id' => 'gtsru'
// 			),
// 		array(
// 			'game_name' => 'gspt.Spin a Win',
// 			'game_code' => 'lwh',
// 			'english_name' => 'Spin a Win',
// 			'external_game_id' => 'lwh'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wheel of Light',
// 			'game_code' => 'gts36',
// 			'english_name' => 'Wheel of Light',
// 			'external_game_id' => 'gts36'
// 			),
// 		array(
// 			'game_name' => 'gspt.Virtual Horses',
// 			'game_code' => 'ashvrth',
// 			'english_name' => 'Virtual Horses',
// 			'external_game_id' => 'ashvrth'
// 			),
// 		),
// ),
// array(
// 	'game_type' => 'Live Dealer Games',
// 	'game_type_lang' => 'gspt_live_dealer_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 		array(
// 			'game_name' => 'gspt.7 Seat Baccarat Live',
// 			'game_code' => '7bal',
// 			'english_name' => '7 Seat Baccarat Live',
// 			'external_game_id' => '7bal'
// 			),
// 		array(
// 			'game_name' => 'gspt.Baccarat Live',
// 			'game_code' => 'bal',
// 			'english_name' => 'Baccarat Live',
// 			'external_game_id' => 'bal'
// 			),
// 		array(
// 			'game_name' => 'gspt.Blackjack Live',
// 			'game_code' => 'bjl',
// 			'english_name' => 'Blackjack Live',
// 			'external_game_id' => 'bjl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Casino Hold’Em Live',
// 			'game_code' => 'chel',
// 			'english_name' => 'Casino Hold’Em Live',
// 			'external_game_id' => 'chel'
// 			),
// 		array(
// 			'game_name' => 'gspt.Exclusive Roulette (Live VIP Roulette)',
// 			'game_code' => 'rodl',
// 			'english_name' => 'Exclusive Roulette (Live VIP Roulette)',
// 			'external_game_id' => 'rodl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Live French Roulette',
// 			'game_code' => 'rofl',
// 			'english_name' => 'Live French Roulette',
// 			'external_game_id' => 'rofl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Progressive Live Baccarat',
// 			'game_code' => 'plba',
// 			'english_name' => 'Progressive Live Baccarat',
// 			'external_game_id' => 'plba'
// 			),
// 		array(
// 			'game_name' => 'gspt.Roulette Live',
// 			'game_code' => 'rol',
// 			'english_name' => 'Roulette Live',
// 			'external_game_id' => 'rol'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sic Bo Live',
// 			'game_code' => 'sbl',
// 			'english_name' => 'Sic Bo Live',
// 			'external_game_id' => 'sbl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Unlimited Blackjack Live',
// 			'game_code' => 'ubjl',
// 			'english_name' => 'Unlimited Blackjack Live',
// 			'external_game_id' => 'ubjl'
// 			),
// 		array(
// 			'game_name' => 'gspt.VIP Baccarat Live',
// 			'game_code' => 'vbal',
// 			'english_name' => 'VIP Baccarat Live',
// 			'external_game_id' => 'vbal'
// 			)
// 		),
// ),
// array(
// 	'game_type' => 'Scratch Cards',
// 	'game_type_lang' => 'gspt_scratch_cards',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 		array(
// 			'game_name' => 'gspt.3 Clowns Scratch',
// 			'game_code' => 'tclsc',
// 			'english_name' => '3 Clowns Scratch',
// 			'external_game_id' => 'tclsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.A Night Out Scratch',
// 			'game_code' => 'gts39',
// 			'english_name' => 'A Night Out Scratch',
// 			'external_game_id' => 'gts39'
// 			),
// 		array(
// 			'game_name' => 'gspt.Avengers Scratch',
// 			'game_code' => 'gtsavgsc',
// 			'english_name' => 'Avengers Scratch',
// 			'external_game_id' => 'gtsavgsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Baywatch Scratch',
// 			'game_code' => 'gtsbwsc',
// 			'english_name' => 'Baywatch Scratch',
// 			'external_game_id' => 'gtsbwsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Beetle Bingo Scratch',
// 			'game_code' => 'bbn',
// 			'english_name' => 'Beetle Bingo Scratch',
// 			'external_game_id' => 'bbn'
// 			),
// 		array(
// 			'game_name' => 'gspt.Blackjack Scratch Card',
// 			'game_code' => 'sbj',
// 			'english_name' => 'Blackjack Scratch Card',
// 			'external_game_id' => 'sbj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Blade Scratch',
// 			'game_code' => 'gts40',
// 			'english_name' => 'Blade Scratch',
// 			'external_game_id' => 'gts40'
// 			),
// 		array(
// 			'game_name' => 'gspt.Captain America – The First Avenger Scratch',
// 			'game_code' => 'gtscnasc',
// 			'english_name' => 'Captain America – The First Avenger Scratch',
// 			'external_game_id' => 'gtscnasc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Classic Slots Scratch Card',
// 			'game_code' => 'scs',
// 			'english_name' => 'Classic Slots Scratch Card',
// 			'external_game_id' => 'scs'
// 			),
// 		array(
// 			'game_name' => 'gspt.Daredevil Scratch',
// 			'game_code' => 'gtsdrdsc',
// 			'english_name' => 'Daredevil Scratch',
// 			'external_game_id' => 'gtsdrdsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dolphin Cash Scratch',
// 			'game_code' => 'gts48',
// 			'english_name' => 'Dolphin Cash Scratch',
// 			'external_game_id' => 'gts48'
// 			),
// 		array(
// 			'game_name' => 'gspt.Easter Surprise Scratch',
// 			'game_code' => 'essc',
// 			'english_name' => 'Easter Surprise Scratch',
// 			'external_game_id' => 'essc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Elektra Scratch',
// 			'game_code' => 'gts44',
// 			'english_name' => 'Elektra Scratch',
// 			'external_game_id' => 'gts44'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fantastic Four Scratch',
// 			'game_code' => 'gts41',
// 			'english_name' => 'Fantastic Four Scratch',
// 			'external_game_id' => 'gts41'
// 			),
// 		array(
// 			'game_name' => 'gspt.Football Mania Scratch',
// 			'game_code' => 'fbm',
// 			'english_name' => 'Football Mania Scratch',
// 			'external_game_id' => 'fbm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ghost Rider Scratch',
// 			'game_code' => 'gtsghrsc',
// 			'english_name' => 'Ghost Rider Scratch',
// 			'external_game_id' => 'gtsghrsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Gladiator Scratch',
// 			'game_code' => 'gts37',
// 			'english_name' => 'Gladiator Scratch',
// 			'external_game_id' => 'gts37'
// 			),
// 		array(
// 			'game_name' => 'gspt.Irish Luck Scratch',
// 			'game_code' => 'gts45',
// 			'english_name' => 'Irish Luck Scratch',
// 			'external_game_id' => 'gts45'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man 2 Scratch',
// 			'game_code' => 'irm3sc',
// 			'english_name' => 'Iron Man 2 Scratch',
// 			'external_game_id' => 'irm3sc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man 3 Scratch',
// 			'game_code' => 'irmn3sc',
// 			'english_name' => 'Iron Man 3 Scratch',
// 			'external_game_id' => 'irmn3sc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Kong Scratch',
// 			'game_code' => 'kkgsc',
// 			'english_name' => 'Kong Scratch',
// 			'external_game_id' => 'kkgsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Lotto Madness Scratch',
// 			'game_code' => 'gts47',
// 			'english_name' => 'Lotto Madness Scratch',
// 			'external_game_id' => 'gts47'
// 			),
// 		array(
// 			'game_name' => 'gspt.Love Match Scratch',
// 			'game_code' => 'lom',
// 			'english_name' => 'Love Match Scratch',
// 			'external_game_id' => 'lom'
// 			),
// 		array(
// 			'game_name' => 'gspt.Mummy Scratch',
// 			'game_code' => 'gts43',
// 			'english_name' => 'Mummy Scratch',
// 			'external_game_id' => 'gts43'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pharaoh’s Kingdom Scratch',
// 			'game_code' => 'pks',
// 			'english_name' => 'Pharaoh’s Kingdom Scratch',
// 			'external_game_id' => 'pks'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pink Panther Scratch',
// 			'game_code' => 'gts42',
// 			'english_name' => 'Pink Panther Scratch',
// 			'external_game_id' => 'gts42'
// 			),
// 		array(
// 			'game_name' => 'gspt.Punisher War Zone Scratch',
// 			'game_code' => 'gtspwzsc',
// 			'english_name' => 'Punisher War Zone Scratch',
// 			'external_game_id' => 'gtspwzsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Rocky Scratch',
// 			'game_code' => 'gtsrkysc',
// 			'english_name' => 'Rocky Scratch',
// 			'external_game_id' => 'gtsrkysc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Roulette Scratch Card',
// 			'game_code' => 'sro',
// 			'english_name' => 'Roulette Scratch Card',
// 			'external_game_id' => 'sro'
// 			),
// 		array(
// 			'game_name' => 'gspt.Santa Scratch',
// 			'game_code' => 'ssa',
// 			'english_name' => 'Santa Scratch',
// 			'external_game_id' => 'ssa'
// 			),
// 		array(
// 			'game_name' => 'gspt.Spider-Man Scratch',
// 			'game_code' => 'gtsspdsc',
// 			'english_name' => 'Spider-Man Scratch',
// 			'external_game_id' => 'gtsspdsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Incredible Hulk Scratch',
// 			'game_code' => 'gtshlksc',
// 			'english_name' => 'The Incredible Hulk Scratch',
// 			'external_game_id' => 'gtshlksc'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Six Million Dollar Man Scratch',
// 			'game_code' => 'gtssmdsc',
// 			'english_name' => 'The Six Million Dollar Man Scratch',
// 			'external_game_id' => 'gtssmdsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Thor Scratch',
// 			'game_code' => 'gtstrmsc',
// 			'english_name' => 'Thor Scratch',
// 			'external_game_id' => 'gtstrmsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps Celebs Scratch',
// 			'game_code' => 'ttcsc',
// 			'english_name' => 'Top Trumps Celebs Scratch',
// 			'external_game_id' => 'ttcsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps Legends Scratch',
// 			'game_code' => 'gtsttlsc',
// 			'english_name' => 'Top Trumps Legends Scratch',
// 			'external_game_id' => 'gtsttlsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Winners Club Scratch',
// 			'game_code' => 'wc',
// 			'english_name' => 'Winners Club Scratch',
// 			'external_game_id' => 'wc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wolverine Scratch',
// 			'game_code' => 'gtswvsc',
// 			'english_name' => 'Wolverine Scratch',
// 			'external_game_id' => 'gtswvsc'
// 			),
// 		array(
// 			'game_name' => 'gspt.X-Men Scratch',
// 			'game_code' => 'gts49',
// 			'english_name' => 'X-Men Scratch',
// 			'external_game_id' => 'gts49'
// 			)
// 		),
// ),
// array(
// 	'game_type' => 'Table Games',
// 	'game_type_lang' => 'gspt_table_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 		array(
// 			'game_name' => 'gspt.3D Roulette',
// 			'game_code' => 'ro3d',
// 			'english_name' => '3D Roulette',
// 			'external_game_id' => 'ro3d'
// 			),
// 		array(
// 			'game_name' => 'gspt.3D Roulette Premium',
// 			'game_code' => 'gtsro3d',
// 			'english_name' => '3D Roulette Premium',
// 			'external_game_id' => 'gtsro3d'
// 			),
// 		array(
// 			'game_name' => 'gspt.American Roulette',
// 			'game_code' => 'rodz',
// 			'english_name' => 'American Roulette',
// 			'external_game_id' => 'rodz'
// 			),
// 		array(
// 			'game_name' => 'gspt.Club Roulette',
// 			'game_code' => 'rouk',
// 			'english_name' => 'Club Roulette',
// 			'external_game_id' => 'rouk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Craps',
// 			'game_code' => 'cr',
// 			'english_name' => 'Craps',
// 			'external_game_id' => 'cr'
// 			),
// 		array(
// 			'game_name' => 'gspt.European Roulette',
// 			'game_code' => 'ro',
// 			'english_name' => 'European Roulette',
// 			'external_game_id' => 'ro'
// 			),
// 		array(
// 			'game_name' => 'gspt.French Roulette',
// 			'game_code' => 'frr',
// 			'english_name' => 'French Roulette',
// 			'external_game_id' => 'frr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Marvel Roulette',
// 			'game_code' => 'rom',
// 			'english_name' => 'Marvel Roulette',
// 			'external_game_id' => 'rom'
// 			),
// 		array(
// 			'game_name' => 'gspt.Multi Wheel Roulette',
// 			'game_code' => 'romw',
// 			'english_name' => 'Multi Wheel Roulette',
// 			'external_game_id' => 'romw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Premium American Roulette',
// 			'game_code' => 'rodz_g',
// 			'english_name' => 'Premium American Roulette',
// 			'external_game_id' => 'rodz_g'
// 			),
// 		array(
// 			'game_name' => 'gspt.Premium European Roulette',
// 			'game_code' => 'ro_g',
// 			'english_name' => 'Premium European Roulette',
// 			'external_game_id' => 'ro_g'
// 			),
// 		array(
// 			'game_name' => 'gspt.Premium French Roulette',
// 			'game_code' => 'frr_g',
// 			'english_name' => 'Premium French Roulette',
// 			'external_game_id' => 'frr_g'
// 			),
// 		array(
// 			'game_name' => 'gspt.Premium Roulette Pro',
// 			'game_code' => 'rop_g',
// 			'english_name' => 'Premium Roulette Pro',
// 			'external_game_id' => 'rop_g'
// 			),
// 		array(
// 			'game_name' => 'gspt.Roulette Pro',
// 			'game_code' => 'rop',
// 			'english_name' => 'Roulette Pro',
// 			'external_game_id' => 'rop'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sic Bo',
// 			'game_code' => 'sb',
// 			'english_name' => 'Sic Bo',
// 			'external_game_id' => 'sb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Video Roulette',
// 			'game_code' => 'gts5',
// 			'english_name' => 'Video Roulette',
// 			'external_game_id' => 'gts5'
// 			)
// 		),
// ),
// array(
// 	'game_type' => 'Video Pokers',
// 	'game_type_lang' => 'gspt_video_pokers',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(


// 		array(
// 			'game_name' => 'gspt.10-line Jacks or Better Progressive',
// 			'game_code' => 'jb10p',
// 			'english_name' => '10-line Jacks or Better Progressive',
// 			'external_game_id' => 'jb10p'
// 			),
// 		array(
// 			'game_name' => 'gspt.2 Ways Royal',
// 			'game_code' => 'hljb',
// 			'english_name' => '2 Ways Royal',
// 			'external_game_id' => 'hljb'
// 			),
// 		array(
// 			'game_name' => 'gspt.25-Line Aces & Faces',
// 			'game_code' => 'af25',
// 			'english_name' => '25-Line Aces & Faces',
// 			'external_game_id' => 'af25'
// 			),
// 		array(
// 			'game_name' => 'gspt.4-Line Aces and Faces',
// 			'game_code' => 'af4',
// 			'english_name' => '4-Line Aces and Faces',
// 			'external_game_id' => 'af4'
// 			),
// 		array(
// 			'game_name' => 'gspt.4-Line Deuces Wild',
// 			'game_code' => 'dw4',
// 			'english_name' => '4-Line Deuces Wild',
// 			'external_game_id' => 'dw4'
// 			),
// 		array(
// 			'game_name' => 'gspt.4-Line Jacks or Better',
// 			'game_code' => 'jb4',
// 			'english_name' => '4-Line Jacks or Better',
// 			'external_game_id' => 'jb4'
// 			),
// 		array(
// 			'game_name' => 'gspt.50-line Jacks or Better',
// 			'game_code' => 'jb50',
// 			'english_name' => '50-line Jacks or Better',
// 			'external_game_id' => 'jb50'
// 			),
// 		array(
// 			'game_name' => 'gspt.50-line Joker Poker',
// 			'game_code' => 'jp50',
// 			'english_name' => '50-line Joker Poker',
// 			'external_game_id' => 'jp50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Aces and Faces',
// 			'game_code' => 'af',
// 			'english_name' => 'Aces and Faces',
// 			'external_game_id' => 'af'
// 			),
// 		array(
// 			'game_name' => 'gspt.All American',
// 			'game_code' => 'amvp',
// 			'english_name' => 'All American',
// 			'external_game_id' => 'amvp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Deuces Wild',
// 			'game_code' => 'dw',
// 			'english_name' => 'Deuces Wild',
// 			'external_game_id' => 'dw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Jacks or Better',
// 			'game_code' => 'po',
// 			'english_name' => 'Jacks or Better',
// 			'external_game_id' => 'po'
// 			),
// 		array(
// 			'game_name' => 'gspt.Jacks or Better Multi-Hand',
// 			'game_code' => 'jb_mh',
// 			'english_name' => 'Jacks or Better Multi-Hand',
// 			'external_game_id' => 'jb_mh'
// 			),
// 		array(
// 			'game_name' => 'gspt.Joker Poker',
// 			'game_code' => 'jp',
// 			'english_name' => 'Joker Poker',
// 			'external_game_id' => 'jp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Mega Jacks',
// 			'game_code' => 'mj',
// 			'english_name' => 'Mega Jacks',
// 			'external_game_id' => 'mj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pick \'Em Poker',
// 			'game_code' => 'pep',
// 			'english_name' => 'Pick \'Em Poker',
// 			'external_game_id' => 'pep'
// 			),
// 		array(
// 			'game_name' => 'gspt.Tens or Better',
// 			'game_code' => 'tob',
// 			'english_name' => 'Tens or Better',
// 			'external_game_id' => 'tob'
// 			),

// 		),
// ),
// array(
// 	'game_type' => 'Video Slots',
// 	'game_type_lang' => 'gspt_video_slots',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(

// 		array(
// 			'game_name' => 'gspt.A Night Out',
// 			'game_code' => 'hb',
// 			'english_name' => 'A Night Out',
// 			'external_game_id' => 'hb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Alien Hunter',
// 			'game_code' => 'ah2',
// 			'english_name' => 'Alien Hunter',
// 			'external_game_id' => 'ah2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Amazon Wild',
// 			'game_code' => 'ashamw',
// 			'english_name' => 'Amazon Wild',
// 			'external_game_id' => 'ashamw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Archer',
// 			'game_code' => 'arc',
// 			'english_name' => 'Archer',
// 			'external_game_id' => 'arc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Arctic Treasure',
// 			'game_code' => 'art',
// 			'english_name' => 'Arctic Treasure',
// 			'external_game_id' => 'art'
// 			),
// 		array(
// 			'game_name' => 'gspt.Atlantis Queen',
// 			'game_code' => 'gtsatq',
// 			'english_name' => 'Atlantis Queen',
// 			'external_game_id' => 'gtsatq'
// 			),
// 		array(
// 			'game_name' => 'gspt.Azteca',
// 			'game_code' => 'azt',
// 			'english_name' => 'Azteca',
// 			'external_game_id' => 'azt'
// 			),
// 		array(
// 			'game_name' => 'gspt.Banana Monkey',
// 			'game_code' => 'bmk',
// 			'english_name' => 'Banana Monkey',
// 			'external_game_id' => 'bmk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Battle of the Gods',
// 			'game_code' => 'gtsbtg',
// 			'english_name' => 'Battle of the Gods',
// 			'external_game_id' => 'gtsbtg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Beach Life',
// 			'game_code' => 'bl',
// 			'english_name' => 'Beach Life',
// 			'external_game_id' => 'bl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Blade',
// 			'game_code' => 'bld',
// 			'english_name' => 'Blade',
// 			'external_game_id' => 'bld'
// 			),
// 		array(
// 			'game_name' => 'gspt.Blade – 50 Lines',
// 			'game_code' => 'bld50',
// 			'english_name' => 'Blade – 50 Lines',
// 			'external_game_id' => 'bld50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Bonus Bears',
// 			'game_code' => 'bob',
// 			'english_name' => 'Bonus Bears',
// 			'external_game_id' => 'bob'
// 			),
// 		array(
// 			'game_name' => 'gspt.Britain’s Got Talent',
// 			'game_code' => 'ashbgt',
// 			'english_name' => 'Britain’s Got Talent',
// 			'external_game_id' => 'ashbgt'
// 			),
// 		array(
// 			'game_name' => 'gspt.Captain America – The First Avenger',
// 			'game_code' => 'cam',
// 			'english_name' => 'Captain America – The First Avenger',
// 			'external_game_id' => 'cam'
// 			),
// 		array(
// 			'game_name' => 'gspt.Captain Cannon’s Circus of Cash',
// 			'game_code' => 'gtscirsj',
// 			'english_name' => 'Captain Cannon’s Circus of Cash',
// 			'external_game_id' => 'gtscirsj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Captain\'s Treasure',
// 			'game_code' => 'ct',
// 			'english_name' => 'Captain\'s Treasure',
// 			'external_game_id' => 'ct'
// 			),
// 		array(
// 			'game_name' => 'gspt.Captain\'s Treasure Pro',
// 			'game_code' => 'ctp2',
// 			'english_name' => 'Captain\'s Treasure Pro',
// 			'external_game_id' => 'ctp2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cat In Vegas',
// 			'game_code' => 'ctiv',
// 			'english_name' => 'Cat In Vegas',
// 			'external_game_id' => 'ctiv'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cat Queen',
// 			'game_code' => 'catqk',
// 			'english_name' => 'Cat Queen',
// 			'external_game_id' => 'catqk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cherry Love',
// 			'game_code' => 'chl',
// 			'english_name' => 'Cherry Love',
// 			'external_game_id' => 'chl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Chinese Kitchen',
// 			'game_code' => 'cm',
// 			'english_name' => 'Chinese Kitchen',
// 			'external_game_id' => 'cm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cinerama',
// 			'game_code' => 'cifr',
// 			'english_name' => 'Cinerama',
// 			'external_game_id' => 'cifr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cops N’ Bandits',
// 			'game_code' => 'gtscnb',
// 			'english_name' => 'Cops N’ Bandits',
// 			'external_game_id' => 'gtscnb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cowboys & Aliens',
// 			'game_code' => 'gtscbl',
// 			'english_name' => 'Cowboys & Aliens',
// 			'external_game_id' => 'gtscbl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Cute and Fluffy',
// 			'game_code' => 'cnf',
// 			'english_name' => 'Cute and Fluffy',
// 			'external_game_id' => 'cnf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Daredevil',
// 			'game_code' => 'drd',
// 			'english_name' => 'Daredevil',
// 			'external_game_id' => 'drd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Daring Dave & The Eye of Ra',
// 			'game_code' => 'gtsdrdv',
// 			'english_name' => 'Daring Dave & The Eye of Ra',
// 			'external_game_id' => 'gtsdrdv'
// 			),
// 		array(
// 			'game_name' => 'gspt.Desert Treasure',
// 			'game_code' => 'dt',
// 			'english_name' => 'Desert Treasure',
// 			'external_game_id' => 'dt'
// 			),
// 		array(
// 			'game_name' => 'gspt.Desert Treasure II',
// 			'game_code' => 'dt2',
// 			'english_name' => 'Desert Treasure II',
// 			'external_game_id' => 'dt2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Diamond Valley',
// 			'game_code' => 'gs',
// 			'english_name' => 'Diamond Valley',
// 			'external_game_id' => 'gs'
// 			),
// 		array(
// 			'game_name' => 'gspt.Diamond Valley Pro',
// 			'game_code' => 'dv2',
// 			'english_name' => 'Diamond Valley Pro',
// 			'external_game_id' => 'dv2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dolphin Cash',
// 			'game_code' => 'gtsdnc',
// 			'english_name' => 'Dolphin Cash',
// 			'external_game_id' => 'gtsdnc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dolphin Reef',
// 			'game_code' => 'dnr',
// 			'english_name' => 'Dolphin Reef',
// 			'external_game_id' => 'dnr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dr Lovemore',
// 			'game_code' => 'dlm',
// 			'english_name' => 'Dr Lovemore',
// 			'external_game_id' => 'dlm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Dragon Kingdom',
// 			'game_code' => 'gtsdgk',
// 			'english_name' => 'Dragon Kingdom',
// 			'external_game_id' => 'gtsdgk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Easter Surprise',
// 			'game_code' => 'eas',
// 			'english_name' => 'Easter Surprise',
// 			'external_game_id' => 'eas'
// 			),
// 		array(
// 			'game_name' => 'gspt.Elektra',
// 			'game_code' => 'elr',
// 			'english_name' => 'Elektra',
// 			'external_game_id' => 'elr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Esmeralda',
// 			'game_code' => 'esmk',
// 			'english_name' => 'Esmeralda',
// 			'external_game_id' => 'esmk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Everybody’s Jackpot',
// 			'game_code' => 'evj',
// 			'english_name' => 'Everybody’s Jackpot',
// 			'external_game_id' => 'evj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fairy Magic',
// 			'game_code' => 'frm',
// 			'english_name' => 'Fairy Magic',
// 			'external_game_id' => 'frm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fantastic Four',
// 			'game_code' => 'fnf',
// 			'english_name' => 'Fantastic Four',
// 			'external_game_id' => 'fnf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fantastic Four 50 Lines',
// 			'game_code' => 'fnf50',
// 			'english_name' => 'Fantastic Four 50 Lines',
// 			'external_game_id' => 'fnf50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Farmer\'s Market',
// 			'game_code' => 'fam3',
// 			'english_name' => 'Farmer\'s Market',
// 			'external_game_id' => 'fam3'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fei Cui Gong Zhu',
// 			'game_code' => 'fcgz',
// 			'english_name' => 'Fei Cui Gong Zhu',
// 			'external_game_id' => 'fcgz'
// 			),
// 		array(
// 			'game_name' => 'gspt.Football Carnival',
// 			'game_code' => 'gtsfc',
// 			'english_name' => 'Football Carnival',
// 			'external_game_id' => 'gtsfc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Football Fans',
// 			'game_code' => 'gtsftf',
// 			'english_name' => 'Football Fans',
// 			'external_game_id' => 'gtsftf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Football Rules!',
// 			'game_code' => 'fbr',
// 			'english_name' => 'Football Rules!',
// 			'external_game_id' => 'fbr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Forest of Wonders',
// 			'game_code' => 'fow',
// 			'english_name' => 'Forest of Wonders',
// 			'external_game_id' => 'fow'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fortune Hill',
// 			'game_code' => 'fth',
// 			'english_name' => 'Fortune Hill',
// 			'external_game_id' => 'fth'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fortune Jump',
// 			'game_code' => 'gtsfj',
// 			'english_name' => 'Fortune Jump',
// 			'external_game_id' => 'gtsfj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fortunes of the Fox',
// 			'game_code' => 'fxf',
// 			'english_name' => 'Fortunes of the Fox',
// 			'external_game_id' => 'fxf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Frankie Dettori‟s Magic Seven Jackpot',
// 			'game_code' => 'fdtjg',
// 			'english_name' => 'Frankie Dettori‟s Magic Seven Jackpot',
// 			'external_game_id' => 'fdtjg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Frankie Dettori‟s™: Magic Seven',
// 			'game_code' => 'fdt',
// 			'english_name' => 'Frankie Dettori‟s™: Magic Seven',
// 			'external_game_id' => 'fdt'
// 			),
// 		array(
// 			'game_name' => 'gspt.From Russia with Love',
// 			'game_code' => 'frl',
// 			'english_name' => 'From Russia with Love',
// 			'external_game_id' => 'frl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fruit Mania',
// 			'game_code' => 'fmn',
// 			'english_name' => 'Fruit Mania',
// 			'external_game_id' => 'fmn'
// 			),
// 		array(
// 			'game_name' => 'gspt.Full Moon Fortunes',
// 			'game_code' => 'ashfmf',
// 			'english_name' => 'Full Moon Fortunes',
// 			'external_game_id' => 'ashfmf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Funky Fruits',
// 			'game_code' => 'fnfrj',
// 			'english_name' => 'Funky Fruits',
// 			'external_game_id' => 'fnfrj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Funky Fruits Farm',
// 			'game_code' => 'fff',
// 			'english_name' => 'Funky Fruits Farm',
// 			'external_game_id' => 'fff'
// 			),
// 		array(
// 			'game_name' => 'gspt.Geisha Story',
// 			'game_code' => 'ges',
// 			'english_name' => 'Geisha Story',
// 			'external_game_id' => 'ges'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ghost Rider',
// 			'game_code' => 'ghr',
// 			'english_name' => 'Ghost Rider',
// 			'external_game_id' => 'ghr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ghosts of Christmas',
// 			'game_code' => 'gtsgoc',
// 			'english_name' => 'Ghosts of Christmas',
// 			'external_game_id' => 'gtsgoc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Gladiator',
// 			'game_code' => 'glr',
// 			'english_name' => 'Gladiator',
// 			'external_game_id' => 'glr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Gladiator Jackpot',
// 			'game_code' => 'glrj',
// 			'english_name' => 'Gladiator Jackpot',
// 			'external_game_id' => 'glrj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Goblin\'s Cave',
// 			'game_code' => 'gc',
// 			'english_name' => 'Goblin\'s Cave',
// 			'external_game_id' => 'gc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Goddess Of Life',
// 			'game_code' => 'gts46',
// 			'english_name' => 'Goddess Of Life',
// 			'external_game_id' => 'gts46'
// 			),
// 		array(
// 			'game_name' => 'gspt.Gold Rally',
// 			'game_code' => 'grel',
// 			'english_name' => 'Gold Rally',
// 			'external_game_id' => 'grel'
// 			),
// 		array(
// 			'game_name' => 'gspt.Golden Games',
// 			'game_code' => 'glg',
// 			'english_name' => 'Golden Games',
// 			'external_game_id' => 'glg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Golden Tour',
// 			'game_code' => 'gos',
// 			'english_name' => 'Golden Tour',
// 			'external_game_id' => 'gos'
// 			),
// 		array(
// 			'game_name' => 'gspt.Great Blue',
// 			'game_code' => 'bib',
// 			'english_name' => 'Great Blue',
// 			'external_game_id' => 'bib'
// 			),
// 		array(
// 			'game_name' => 'gspt.Greatest Odyssey',
// 			'game_code' => 'gro',
// 			'english_name' => 'Greatest Odyssey',
// 			'external_game_id' => 'gro'
// 			),
// 		array(
// 			'game_name' => 'gspt.Halloween Fortune',
// 			'game_code' => 'hlf',
// 			'english_name' => 'Halloween Fortune',
// 			'external_game_id' => 'hlf'
// 			),
// 		array(
// 			'game_name' => 'gspt.Happy Bugs',
// 			'game_code' => 'hpb',
// 			'english_name' => 'Happy Bugs',
// 			'external_game_id' => 'hpb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Heart of the Jungle',
// 			'game_code' => 'ashhotj',
// 			'english_name' => 'Heart of the Jungle',
// 			'external_game_id' => 'ashhotj'
// 			),
// 		array(
// 			'game_name' => 'gspt.Highway Kings',
// 			'game_code' => 'hk',
// 			'english_name' => 'Highway Kings',
// 			'external_game_id' => 'hk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Highway Kings Pro',
// 			'game_code' => 'gtshwkp',
// 			'english_name' => 'Highway Kings Pro',
// 			'external_game_id' => 'gtshwkp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Hot Gems',
// 			'game_code' => 'gts50',
// 			'english_name' => 'Hot Gems',
// 			'external_game_id' => 'gts50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Hulk With Marvel Jackpot',
// 			'game_code' => 'hlk2',
// 			'english_name' => 'Hulk With Marvel Jackpot',
// 			'external_game_id' => 'hlk2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ice Hockey',
// 			'game_code' => 'iceh',
// 			'english_name' => 'Ice Hockey',
// 			'external_game_id' => 'iceh'
// 			),
// 		array(
// 			'game_name' => 'gspt.Innocence or Temptation',
// 			'game_code' => 'gtsaod',
// 			'english_name' => 'Innocence or Temptation',
// 			'external_game_id' => 'gtsaod'
// 			),
// 		array(
// 			'game_name' => 'gspt.Irish Luck',
// 			'game_code' => 'irl (Flash gamecode: gtsirl)',
// 			'english_name' => 'Irish Luck',
// 			'external_game_id' => 'irl (Flash gamecode: gtsirl)'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man 2',
// 			'game_code' => 'irm3',
// 			'english_name' => 'Iron Man 2',
// 			'external_game_id' => 'irm3'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man 2 50 Lines',
// 			'game_code' => 'irm50',
// 			'english_name' => 'Iron Man 2 50 Lines',
// 			'external_game_id' => 'irm50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man 3',
// 			'game_code' => 'irmn3',
// 			'english_name' => 'Iron Man 3',
// 			'external_game_id' => 'irmn3'
// 			),
// 		array(
// 			'game_name' => 'gspt.Iron Man with Marvel Jackpot',
// 			'game_code' => 'irm2',
// 			'english_name' => 'Iron Man with Marvel Jackpot',
// 			'external_game_id' => 'irm2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Jackpot Giant',
// 			'game_code' => 'jpgt',
// 			'english_name' => 'Jackpot Giant',
// 			'external_game_id' => 'jpgt'
// 			),
// 		array(
// 			'game_name' => 'gspt.John Wayne',
// 			'game_code' => 'gtsjhw',
// 			'english_name' => 'John Wayne',
// 			'external_game_id' => 'gtsjhw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Kong The Eighth Wonder of The World',
// 			'game_code' => 'kkg',
// 			'english_name' => 'Kong The Eighth Wonder of The World',
// 			'external_game_id' => 'kkg'
// 			),
// 		array(
// 			'game_name' => 'gspt.La Chatte Rouge',
// 			'game_code' => 'lcr',
// 			'english_name' => 'La Chatte Rouge',
// 			'external_game_id' => 'lcr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Lucky Panda',
// 			'game_code' => 'gts51',
// 			'english_name' => 'Lucky Panda',
// 			'external_game_id' => 'gts51'
// 			),
// 		array(
// 			'game_name' => 'gspt.Marilyn Monroe',
// 			'game_code' => 'gtsmrln',
// 			'english_name' => 'Marilyn Monroe',
// 			'external_game_id' => 'gtsmrln'
// 			),
// 		array(
// 			'game_name' => 'gspt.Mr. Cashback',
// 			'game_code' => 'mcb',
// 			'english_name' => 'Mr. Cashback',
// 			'external_game_id' => 'mcb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Nian Nian You Yu',
// 			'game_code' => 'nian_k',
// 			'english_name' => 'Nian Nian You Yu',
// 			'external_game_id' => 'nian_k'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ocean Princess',
// 			'game_code' => 'op',
// 			'english_name' => 'Ocean Princess',
// 			'external_game_id' => 'op'
// 			),
// 		array(
// 			'game_name' => 'gspt.Panther Moon',
// 			'game_code' => 'pmn',
// 			'english_name' => 'Panther Moon',
// 			'external_game_id' => 'pmn'
// 			),
// 		array(
// 			'game_name' => 'gspt.Penguin Vacation',
// 			'game_code' => 'pgv',
// 			'english_name' => 'Penguin Vacation',
// 			'external_game_id' => 'pgv'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pharaoh\'s Secrets',
// 			'game_code' => 'pst',
// 			'english_name' => 'Pharaoh\'s Secrets',
// 			'external_game_id' => 'pst'
// 			),
// 		array(
// 			'game_name' => 'gspt.Piggies and the Wolf',
// 			'game_code' => 'paw',
// 			'english_name' => 'Piggies and the Wolf',
// 			'external_game_id' => 'paw'
// 			),
// 		array(
// 			'game_name' => 'gspt.Pink Panther',
// 			'game_code' => 'pnp',
// 			'english_name' => 'Pink Panther',
// 			'external_game_id' => 'pnp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Plenty O\'Fortune',
// 			'game_code' => 'gtspor',
// 			'english_name' => 'Plenty O\'Fortune',
// 			'external_game_id' => 'gtspor'
// 			),
// 		array(
// 			'game_name' => 'gspt.Punisher War Zone',
// 			'game_code' => 'pwz',
// 			'english_name' => 'Punisher War Zone',
// 			'external_game_id' => 'pwz'
// 			),
// 		array(
// 			'game_name' => 'gspt.Purple Hot',
// 			'game_code' => 'photk',
// 			'english_name' => 'Purple Hot',
// 			'external_game_id' => 'photk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Queen of the Pyramids',
// 			'game_code' => 'qop',
// 			'english_name' => 'Queen of the Pyramids',
// 			'external_game_id' => 'qop'
// 			),
// 		array(
// 			'game_name' => 'gspt.Rocky',
// 			'game_code' => 'rky',
// 			'english_name' => 'Rocky',
// 			'external_game_id' => 'rky'
// 			),
// 		array(
// 			'game_name' => 'gspt.Rome and Glory',
// 			'game_code' => 'rng2 (Flash gamecode: gtsrng)',
// 			'english_name' => 'Rome and Glory',
// 			'external_game_id' => 'rng2 (Flash gamecode: gtsrng)'
// 			),
// 		array(
// 			'game_name' => 'gspt.Safari Heat',
// 			'game_code' => 'sfh',
// 			'english_name' => 'Safari Heat',
// 			'external_game_id' => 'sfh'
// 			),
// 		array(
// 			'game_name' => 'gspt.Samba Brazil',
// 			'game_code' => 'gtssmbr',
// 			'english_name' => 'Samba Brazil',
// 			'external_game_id' => 'gtssmbr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Santa Surprise',
// 			'game_code' => 'ssp',
// 			'english_name' => 'Santa Surprise',
// 			'external_game_id' => 'ssp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Secrets of the Amazon™',
// 			'game_code' => 'samz',
// 			'english_name' => 'Secrets of the Amazon™',
// 			'external_game_id' => 'samz'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sherlock Mystery',
// 			'game_code' => 'shmst',
// 			'english_name' => 'Sherlock Mystery',
// 			'external_game_id' => 'shmst'
// 			),
// 		array(
// 			'game_name' => 'gspt.Silent Samurai',
// 			'game_code' => 'sis',
// 			'english_name' => 'Silent Samurai',
// 			'external_game_id' => 'sis'
// 			),
// 		array(
// 			'game_name' => 'gspt.Silver Bullet',
// 			'game_code' => 'sib',
// 			'english_name' => 'Silver Bullet',
// 			'external_game_id' => 'sib'
// 			),
// 		array(
// 			'game_name' => 'gspt.Skazka Pro',
// 			'game_code' => 'skp',
// 			'english_name' => 'Skazka Pro',
// 			'external_game_id' => 'skp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sparta',
// 			'game_code' => 'spr',
// 			'english_name' => 'Sparta',
// 			'external_game_id' => 'spr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Spider-Man: Attack of The Green Goblin',
// 			'game_code' => 'spidc',
// 			'english_name' => 'Spider-Man: Attack of The Green Goblin',
// 			'external_game_id' => 'spidc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Streak of Luck',
// 			'game_code' => 'sol',
// 			'english_name' => 'Streak of Luck',
// 			'external_game_id' => 'sol'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sultan’s Gold',
// 			'game_code' => 'gtsstg',
// 			'english_name' => 'Sultan’s Gold',
// 			'external_game_id' => 'gtsstg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sunset Beach',
// 			'game_code' => 'snsb',
// 			'english_name' => 'Sunset Beach',
// 			'external_game_id' => 'snsb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sweet Party™',
// 			'game_code' => 'cnpr',
// 			'english_name' => 'Sweet Party™',
// 			'external_game_id' => 'cnpr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Tennis Stars',
// 			'game_code' => 'tst',
// 			'english_name' => 'Tennis Stars',
// 			'external_game_id' => 'tst'
// 			),
// 		array(
// 			'game_name' => 'gspt.Thai Paradise',
// 			'game_code' => 'tpd2',
// 			'english_name' => 'Thai Paradise',
// 			'external_game_id' => 'tpd2'
// 			),
// 		array(
// 			'game_name' => 'gspt.Thai Temple ',
// 			'game_code' => 'thtk ',
// 			'english_name' => 'Thai Temple ',
// 			'external_game_id' => 'thtk '
// 			),
// 		array(
// 			'game_name' => 'gspt.The Avengers',
// 			'game_code' => 'avng',
// 			'english_name' => 'The Avengers',
// 			'external_game_id' => 'avng'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Discovery',
// 			'game_code' => 'dcv',
// 			'english_name' => 'The Discovery',
// 			'external_game_id' => 'dcv'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Incredible Hulk 50 Lines',
// 			'game_code' => 'hlk50',
// 			'english_name' => 'The Incredible Hulk 50 Lines',
// 			'external_game_id' => 'hlk50'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Jazz Club',
// 			'game_code' => 'gtsjzc',
// 			'english_name' => 'The Jazz Club',
// 			'external_game_id' => 'gtsjzc'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Love Boat',
// 			'game_code' => 'lvb',
// 			'english_name' => 'The Love Boat',
// 			'external_game_id' => 'lvb'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Mummy',
// 			'game_code' => 'mmy',
// 			'english_name' => 'The Mummy',
// 			'external_game_id' => 'mmy'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Pyramid of Ramesses',
// 			'game_code' => 'pyrrk',
// 			'english_name' => 'The Pyramid of Ramesses',
// 			'external_game_id' => 'pyrrk'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Six Million Dollar Man',
// 			'game_code' => 'gtssmdm',
// 			'english_name' => 'The Six Million Dollar Man',
// 			'external_game_id' => 'gtssmdm'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Three Musketeers and the Queen’s Diamond™',
// 			'game_code' => 'tmqd',
// 			'english_name' => 'The Three Musketeers and the Queen’s Diamond™',
// 			'external_game_id' => 'tmqd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Thor The Mighty Avenger',
// 			'game_code' => 'trm',
// 			'english_name' => 'Thor The Mighty Avenger',
// 			'external_game_id' => 'trm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Thrill Seekers',
// 			'game_code' => 'ts',
// 			'english_name' => 'Thrill Seekers',
// 			'external_game_id' => 'ts'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps Celebs',
// 			'game_code' => 'ttc',
// 			'english_name' => 'Top Trumps Celebs',
// 			'external_game_id' => 'ttc'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps Football Legends',
// 			'game_code' => 'ttl',
// 			'english_name' => 'Top Trumps Football Legends',
// 			'external_game_id' => 'ttl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps World Football Stars',
// 			'game_code' => 'tfs',
// 			'english_name' => 'Top Trumps World Football Stars',
// 			'external_game_id' => 'tfs'
// 			),
// 		array(
// 			'game_name' => 'gspt.Triple Profits',
// 			'game_code' => 'tp',
// 			'english_name' => 'Triple Profits',
// 			'external_game_id' => 'tp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Tropic Reels',
// 			'game_code' => 'tr',
// 			'english_name' => 'Tropic Reels',
// 			'external_game_id' => 'tr'
// 			),
// 		array(
// 			'game_name' => 'gspt.True Love',
// 			'game_code' => 'trl',
// 			'english_name' => 'True Love',
// 			'external_game_id' => 'trl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ugga Bugga',
// 			'game_code' => 'ub',
// 			'english_name' => 'Ugga Bugga',
// 			'external_game_id' => 'ub'
// 			),
// 		array(
// 			'game_name' => 'gspt.Vacation Station',
// 			'game_code' => 'er',
// 			'english_name' => 'Vacation Station',
// 			'external_game_id' => 'er'
// 			),
// 		array(
// 			'game_name' => 'gspt.Vacation Station Deluxe',
// 			'game_code' => 'vcstd',
// 			'english_name' => 'Vacation Station Deluxe',
// 			'external_game_id' => 'vcstd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Vikingmania',
// 			'game_code' => 'gts52',
// 			'english_name' => 'Vikingmania',
// 			'external_game_id' => 'gts52'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wall St. Fever',
// 			'game_code' => 'wsffr',
// 			'english_name' => 'Wall St. Fever',
// 			'external_game_id' => 'wsffr'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wanted Dead or Alive',
// 			'game_code' => 'wan',
// 			'english_name' => 'Wanted Dead or Alive',
// 			'external_game_id' => 'wan'
// 			),
// 		array(
// 			'game_name' => 'gspt.What\'s Cooking',
// 			'game_code' => 'whc',
// 			'english_name' => 'What\'s Cooking',
// 			'external_game_id' => 'whc'
// 			),
// 		array(
// 			'game_name' => 'gspt.White King',
// 			'game_code' => 'whk',
// 			'english_name' => 'White King',
// 			'external_game_id' => 'whk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wild Gambler',
// 			'game_code' => 'gtswg',
// 			'english_name' => 'Wild Gambler',
// 			'external_game_id' => 'gtswg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wild Games',
// 			'game_code' => 'gtslgms',
// 			'english_name' => 'Wild Games',
// 			'external_game_id' => 'gtslgms'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wild Spirit',
// 			'game_code' => 'wis',
// 			'english_name' => 'Wild Spirit',
// 			'external_game_id' => 'wis'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wings of Gold',
// 			'game_code' => 'gtswng',
// 			'english_name' => 'Wings of Gold',
// 			'external_game_id' => 'gtswng'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wolverine',
// 			'game_code' => 'wvm',
// 			'english_name' => 'Wolverine',
// 			'external_game_id' => 'wvm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wu Long',
// 			'game_code' => 'wlg',
// 			'english_name' => 'Wu Long',
// 			'external_game_id' => 'wlg'
// 			),
// 		array(
// 			'game_name' => 'gspt.X-Men',
// 			'game_code' => 'xmn',
// 			'english_name' => 'X-Men',
// 			'external_game_id' => 'xmn'
// 			),
// 		array(
// 			'game_name' => 'gspt.X-Men 50 Lines',
// 			'game_code' => 'xmn50',
// 			'english_name' => 'X-Men 50 Lines',
// 			'external_game_id' => 'xmn50'
// 			),
// 		array(
// 			'game_name' => 'gspt.Zhao Cai Jin Bao',
// 			'game_code' => 'zcjb',
// 			'english_name' => 'Zhao Cai Jin Bao',
// 			'external_game_id' => 'zcjb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fortunate 5',
// 			'game_code' => 'frtf',
// 			'english_name' => 'Fortunate 5',
// 			'external_game_id' => 'frtf'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Riches of Don Quixote',
// 			'game_code' => 'donq',
// 			'english_name' => 'The Riches of Don Quixote',
// 			'external_game_id' => 'donq'
// 			),
// 		array(
// 			'game_name' => 'gspt.The Alchemist\'s Spell',
// 			'game_code' => 'tglalcs',
// 			'english_name' => 'The Alchemist\'s Spell',
// 			'external_game_id' => 'tglalcs'
// 			),
// 		array(
// 			'game_name' => 'gspt.Chests of Plenty',
// 			'game_code' => 'ashcpl',
// 			'english_name' => 'Chests of Plenty',
// 			'external_game_id' => 'ashcpl'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sinbad\'s Golden Voyage',
// 			'game_code' => 'ashsbd',
// 			'english_name' => 'Sinbad\'s Golden Voyage',
// 			'external_game_id' => 'ashsbd'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ice run',
// 			'game_code' => 'gtsir',
// 			'english_name' => 'Ice run',
// 			'external_game_id' => 'gtsir'
// 			),

// 		array(
// 			'game_name' => 'gspt.The Great Ming Empire',
// 			'game_code' => 'gtsgme',
// 			'english_name' => 'The Great Ming Empire',
// 			'external_game_id' => 'gtsgme'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fei Long Zai Tian',
// 			'game_code' => 'gtsflzt',
// 			'english_name' => 'Fei Long Zai Tian',
// 			'external_game_id' => 'gtsflzt'
// 			),
// 		array(
// 			'game_name' => 'gspt.Wild Gambler: Arctic Adventure',
// 			'game_code' => 'ashwgaa',
// 			'english_name' => 'Wild Gambler: Arctic Adventure',
// 			'external_game_id' => 'ashwgaa'
// 			),
// 		array(
// 			'game_name' => 'gspt.Monty Python\'s Spamalot Slot',
// 			'game_code' => 'spm',
// 			'english_name' => 'Monty Python\'s Spamalot Slot',
// 			'external_game_id' => 'spm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Lotto Madness',
// 			'game_code' => 'lm',
// 			'english_name' => 'Lotto Madness',
// 			'external_game_id' => 'lm'
// 			),
// 		array(
// 			'game_name' => 'gspt.Bounty of the Beanstalk',
// 			'game_code' => 'ashbob',
// 			'english_name' => 'Bounty of the Beanstalk',
// 			'external_game_id' => 'ashbob'
// 			),
// 		array(
// 			'game_name' => 'gspt.Fairest of them All',
// 			'game_code' => 'ashfta',
// 			'english_name' => 'Fairest of them All',
// 			'external_game_id' => 'ashfta'
// 			),
// 		array(
// 			'game_name' => 'gspt.Magical Stacks (NEW!)',
// 			'game_code' => 'mgstk',
// 			'english_name' => 'Magical Stacks (NEW!)',
// 			'external_game_id' => 'mgstk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Ji Xiang 8 (NEW!)',
// 			'game_code' => 'gtsjxb',
// 			'english_name' => 'Ji Xiang 8 (NEW!)',
// 			'external_game_id' => 'gtsjxb'
// 			),
// 		array(
// 			'game_name' => 'gspt.Sun Wukong (NEW!)',
// 			'game_code' => 'gtsswk',
// 			'english_name' => 'Sun Wukong (NEW!)',
// 			'external_game_id' => 'gtsswk'
// 			),
// 		array(
// 			'game_name' => 'gspt.Bai Shi (NEW!)',
// 			'game_code' => 'bs',
// 			'english_name' => 'Bai Shi (NEW!)',
// 			'external_game_id' => 'bs'
// 			),
// 		array(
// 			'game_name' => 'gspt.Zhao Cai Jin Bao Jackpot (NEW!)',
// 			'game_code' => 'zcjbjp',
// 			'english_name' => 'Zhao Cai Jin Bao Jackpot (NEW!)',
// 			'external_game_id' => 'zcjbjp'
// 			),
// 		array(
// 			'game_name' => 'gspt.Zhao Cai Tong Zi (NEW!)',
// 			'game_code' => 'zctz',
// 			'english_name' => 'Zhao Cai Tong Zi (NEW!)',
// 			'external_game_id' => 'zctz'
// 			),
// 		array(
// 			'game_name' => 'gspt.Si Xiang (NEW!)',
// 			'game_code' => 'sx',
// 			'english_name' => 'Si Xiang (NEW!)',
// 			'external_game_id' => 'sx'
// 			),
// 		array(
// 			'game_name' => 'gspt.Monty Python\'s Life of Brian (NEW!)',
// 			'game_code' => 'ashlob',
// 			'english_name' => 'Monty Python\'s Life of Brian (NEW!)',
// 			'external_game_id' => 'ashlob'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Gun (NEW!)',
// 			'game_code' => 'topg',
// 			'english_name' => 'Top Gun (NEW!)',
// 			'external_game_id' => 'topg'
// 			),
// 		array(
// 			'game_name' => 'gspt.Top Trumps Football Stars 2014 (NEW!)',
// 			'game_code' => 'ttwfs',
// 			'english_name' => 'Top Trumps Football Stars 2014 (NEW!)',
// 			'external_game_id' => 'ttwfs'
// 			)
// 		),
// ),
// array(
// 	'game_type' => 'Live Games',
// 	'game_type_lang' => 'gspt_live_games',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(
// 		array(
// 			'game_name' => 'gspt.Prestige Roulette (Euro Live) (NEW!)',
// 			'game_code' => 'mcrol',
// 			'english_name' => 'Prestige Roulette (Euro Live) (NEW!)',
// 			'external_game_id' => 'mcrol'
// 			)
// 		)
// 	),
// array(
// 	'game_type' => 'Arcade',
// 	'game_type_lang' => 'gspt_arcade',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_TRUE,
// 	'game_description_list' => array(
// 		array(
// 			'game_name' => 'gspt.Virtual Dogs (NEW!)',
// 			'game_code' => 'ashvrtd',
// 			'english_name' => 'Virtual Dogs (NEW!)',
// 			'external_game_id' => 'ashvrtd'
// 			)
// 		)
// 	),
// array(
// 	'game_type' => 'unknown',
// 	'game_type_lang' => 'gspt.unknown',
// 	'status' => self::FLAG_TRUE,
// 	'flag_show_in_site' => self::FLAG_FALSE,
// 	'game_description_list' => array(

// 		),
// 	)
// );

// $game_description_list = array();
// foreach ($data as $game_type) {

// 	$this->db->insert('game_type', array(
// 		'game_platform_id' => GSPT_API,
// 		'game_type' => $game_type['game_type'],
// 		'game_type_lang' => $game_type['game_type_lang'],
// 		'status' => $game_type['status'],
// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
// 		));

// 	$game_type_id = $this->db->insert_id();
// 	foreach ($game_type['game_description_list'] as $game_description) {
// 		$game_description_list[] = array_merge(array(
// 			'game_platform_id' => GSPT_API,
// 			'game_type_id' => $game_type_id,
// 			), $game_description);
// 	}

// }

// $this->db->insert_batch('game_description', $game_description_list);
// $this->db->trans_complete();

}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => GSPT_API));
	// $this->db->delete('game_description', array('game_platform_id' => GSPT_API));
	// $this->db->trans_complete();
}
}