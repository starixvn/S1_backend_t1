<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_isb_game_names_in_game_description_20161103_remove extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		// $this->db->trans_start();

		// $game_descriptions = array(
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882026',
		// 		'game_name' => '_json:{"1":"100x Play","2":"100x Play"}',
		// 		'english_name' => '100x Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882022',
		// 		'game_name' => '_json:{"1":"10x Deuce Wild","2":"10x Deuce Wild"}',
		// 		'english_name' => '10x Deuce Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882001',
		// 		'game_name' => '_json:{"1":"10x Play","2":"10x Play"}',
		// 		'english_name' => '10x Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882002',
		// 		'game_name' => '_json:{"1":"2 Deuce Wild","2":"2 Deuce Wild"}',
		// 		'english_name' => '2 Deuce Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881594',
		// 		'game_name' => '_json:{"1":"24™","2":"24™"}',
		// 		'english_name' => '24™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906546',
		// 		'game_name' => '_json:{"1":"24™","2":"24™"}',
		// 		'english_name' => '24™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882023',
		// 		'game_name' => '_json:{"1":"25x Deuces Poker","2":"25x Deuces Poker"}',
		// 		'english_name' => '25x Deuces Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882003',
		// 		'game_name' => '_json:{"1":"25x Play","2":"25x Play"}',
		// 		'english_name' => '25x Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881500',
		// 		'game_name' => '_json:{"1":"3 Hit Pay","2":"3 Hit Pay"}',
		// 		'english_name' => '3 Hit Pay',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882021',
		// 		'game_name' => '_json:{"1":"3x Deuce Poker","2":"3x Deuce Poker"}',
		// 		'english_name' => '3x Deuce Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882004',
		// 		'game_name' => '_json:{"1":"3x Double Play","2":"3x Double Play"}',
		// 		'english_name' => '3x Double Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882005',
		// 		'game_name' => '_json:{"1":"3x Joker Play","2":"3x Joker Play"}',
		// 		'english_name' => '3x Joker Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882006',
		// 		'game_name' => '_json:{"1":"3x Play","2":"3x Play"}',
		// 		'english_name' => '3x Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882034',
		// 		'game_name' => '_json:{"1":"4x Deuce Wild","2":"4x Deuce Wild"}',
		// 		'english_name' => '4x Deuce Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882033',
		// 		'game_name' => '_json:{"1":"4x Tens or Better","2":"4x Tens or Better"}',
		// 		'english_name' => '4x Tens or Better',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882028',
		// 		'game_name' => '_json:{"1":"4x Vegas Joker","2":"4x Vegas Joker"}',
		// 		'english_name' => '4x Vegas Joker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882020',
		// 		'game_name' => '_json:{"1":"50x Play","2":"50x Play"}',
		// 		'english_name' => '50x Play',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881507',
		// 		'game_name' => '_json:{"1":"Absolute Super Reels","2":"Absolute Super Reels"}',
		// 		'english_name' => 'Absolute Super Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906500',
		// 		'game_name' => '_json:{"1":"Absolute Super Reels","2":"Absolute Super Reels"}',
		// 		'english_name' => 'Absolute Super Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881525',
		// 		'game_name' => '_json:{"1":"Alice Adventure","2":"Alice Adventure"}',
		// 		'english_name' => 'Alice Adventure',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906504',
		// 		'game_name' => '_json:{"1":"Alice Adventure","2":"Alice Adventure"}',
		// 		'english_name' => 'Alice Adventure',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881565',
		// 		'game_name' => '_json:{"1":"Aliens Attack","2":"Aliens Attack"}',
		// 		'english_name' => 'Aliens Attack',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881578',
		// 		'game_name' => '_json:{"1":"Ambiance","2":"Ambiance"}',
		// 		'english_name' => 'Ambiance',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896527',
		// 		'game_name' => '_json:{"1":"Ambiance","2":"Ambiance"}',
		// 		'english_name' => 'Ambiance',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883014',
		// 		'game_name' => '_json:{"1":"American Roulette","2":"American Roulette"}',
		// 		'english_name' => 'American Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881591',
		// 		'game_name' => '_json:{"1":"Astro Magic","2":"Astro Magic"}',
		// 		'english_name' => 'Astro Magic',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906544',
		// 		'game_name' => '_json:{"1":"Astro Magic","2":"Astro Magic"}',
		// 		'english_name' => 'Astro Magic',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883006',
		// 		'game_name' => '_json:{"1":"Baccarat","2":"Baccarat"}',
		// 		'english_name' => 'Baccarat',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881513',
		// 		'game_name' => '_json:{"1":"Basic Instinct™","2":"Basic Instinct™"}',
		// 		'english_name' => 'Basic Instinct™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906507',
		// 		'game_name' => '_json:{"1":"Basic Instinct™","2":"Basic Instinct™"}',
		// 		'english_name' => 'Basic Instinct™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881573',
		// 		'game_name' => '_json:{"1":"Best things in life","2":"Best things in life"}',
		// 		'english_name' => 'Best things in life',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896528',
		// 		'game_name' => '_json:{"1":"Best things in life","2":"Best things in life"}',
		// 		'english_name' => 'Best things in life',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881570',
		// 		'game_name' => '_json:{"1":"Beverly Hills 90210™","2":"Beverly Hills 90210™"}',
		// 		'english_name' => 'Beverly Hills 90210™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906523',
		// 		'game_name' => '_json:{"1":"Beverly Hills 90210™","2":"Beverly Hills 90210™"}',
		// 		'english_name' => 'Beverly Hills 90210™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881508',
		// 		'game_name' => '_json:{"1":"Bewitched","2":"Bewitched"}',
		// 		'english_name' => 'Bewitched',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906517',
		// 		'game_name' => '_json:{"1":"Bewitched","2":"Bewitched"}',
		// 		'english_name' => 'Bewitched',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883007',
		// 		'game_name' => '_json:{"1":"Blackjack","2":"Blackjack"}',
		// 		'english_name' => 'Blackjack',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896522',
		// 		'game_name' => '_json:{"1":"Blackjack","2":"Blackjack"}',
		// 		'english_name' => 'Blackjack',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883023',
		// 		'game_name' => '_json:{"1":"Blackjack Atlantic City","2":"Blackjack Atlantic City"}',
		// 		'english_name' => 'Blackjack Atlantic City',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883033',
		// 		'game_name' => '_json:{"1":"Blackjack French","2":"Blackjack French"}',
		// 		'english_name' => 'Blackjack French',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883048',
		// 		'game_name' => '_json:{"1":"Blackjack Multi Hand 3D","2":"Blackjack Multi Hand 3D"}',
		// 		'english_name' => 'Blackjack Multi Hand 3D',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883016',
		// 		'game_name' => '_json:{"1":"Blackjack Multihand","2":"Blackjack Multihand"}',
		// 		'english_name' => 'Blackjack Multihand',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883037',
		// 		'game_name' => '_json:{"1":"Blackjack Multihand VIP","2":"Blackjack Multihand VIP"}',
		// 		'english_name' => 'Blackjack Multihand VIP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883034',
		// 		'game_name' => '_json:{"1":"Blackjack Reno","2":"Blackjack Reno"}',
		// 		'english_name' => 'Blackjack Reno',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883039',
		// 		'game_name' => '_json:{"1":"Blackjack Super 7’s Multihand","2":"Blackjack Super 7’s Multihand"}',
		// 		'english_name' => 'Blackjack Super 7’s Multihand',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883029',
		// 		'game_name' => '_json:{"1":"Blackjack VIP","2":"Blackjack VIP"}',
		// 		'english_name' => 'Blackjack VIP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883044',
		// 		'game_name' => '_json:{"1":"Bonus Roulette","2":"Bonus Roulette"}',
		// 		'english_name' => 'Bonus Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881270',
		// 		'game_name' => '_json:{"1":"Bug’s World","2":"Bug’s World"}',
		// 		'english_name' => 'Bug’s World',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883038',
		// 		'game_name' => '_json:{"1":"Casino High Low","2":"Casino High Low"}',
		// 		'english_name' => 'Casino High Low',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883024',
		// 		'game_name' => '_json:{"1":"Casino Hold’em","2":"Casino Hold’em"}',
		// 		'english_name' => 'Casino Hold’em',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '​881597',
		// 		'game_name' => '_json:{"1":"Cloud Tales","2":"Cloud Tales"}',
		// 		'english_name' => 'Cloud Tales',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896551',
		// 		'game_name' => '_json:{"1":"Cloud Tales","2":"Cloud Tales"}',
		// 		'english_name' => 'Cloud Tales',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882032',
		// 		'game_name' => '_json:{"1":"Deuce Wild Progressive","2":"Deuce Wild Progressive"}',
		// 		'english_name' => 'Deuce Wild Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882027',
		// 		'game_name' => '_json:{"1":"Deuces and Joker Poker","2":"Deuces and Joker Poker"}',
		// 		'english_name' => 'Deuces and Joker Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882039',
		// 		'game_name' => '_json:{"1":"Deuces Wild 4UP","2":"Deuces Wild 4UP"}',
		// 		'english_name' => 'Deuces Wild 4UP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881505',
		// 		'game_name' => '_json:{"1":"Diamond Wild","2":"Diamond Wild"}',
		// 		'english_name' => 'Diamond Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906508',
		// 		'game_name' => '_json:{"1":"Diamond Wild","2":"Diamond Wild"}',
		// 		'english_name' => 'Diamond Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881586',
		// 		'game_name' => '_json:{"1":"Dolphin’s Island","2":"Dolphin’s Island"}',
		// 		'english_name' => 'Dolphin’s Island',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896542',
		// 		'game_name' => '_json:{"1":"Dolphin’s Island","2":"Dolphin’s Island"}',
		// 		'english_name' => 'Dolphin’s Island',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882019',
		// 		'game_name' => '_json:{"1":"Double Joker","2":"Double Joker"}',
		// 		'english_name' => 'Double Joker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883020',
		// 		'game_name' => '_json:{"1":"Easy Roulette","2":"Easy Roulette"}',
		// 		'english_name' => 'Easy Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883017',
		// 		'game_name' => '_json:{"1":"European Progressive Roulette","2":"European Progressive Roulette"}',
		// 		'english_name' => 'European Progressive Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883021',
		// 		'game_name' => '_json:{"1":"European Roulette","2":"European Roulette"}',
		// 		'english_name' => 'European Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896521',
		// 		'game_name' => '_json:{"1":"European Roulette","2":"European Roulette"}',
		// 		'english_name' => 'European Roulette',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883012',
		// 		'game_name' => '_json:{"1":"European Roulette Small Bets","2":"European Roulette Small Bets"}',
		// 		'english_name' => 'European Roulette Small Bets',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881527',
		// 		'game_name' => '_json:{"1":"Fruit Boxes","2":"Fruit Boxes"}',
		// 		'english_name' => 'Fruit Boxes',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881556',
		// 		'game_name' => '_json:{"1":"Gifts From Caesar","2":"Gifts From Caesar"}',
		// 		'english_name' => 'Gifts From Caesar',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881547',
		// 		'game_name' => '_json:{"1":"Gold Hold","2":"Gold Hold"}',
		// 		'english_name' => 'Gold Hold',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881590',
		// 		'game_name' => '_json:{"1":"Hansel & Gretel: Witch Hunters™","2":"Hansel & Gretel: Witch Hunters™"}',
		// 		'english_name' => 'Hansel & Gretel: Witch Hunters™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906545',
		// 		'game_name' => '_json:{"1":"Hansel & Gretel: Witch Hunters™","2":"Hansel & Gretel: Witch Hunters™"}',
		// 		'english_name' => 'Hansel & Gretel: Witch Hunters™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881572',
		// 		'game_name' => '_json:{"1":"Happy Birds","2":"Happy Birds"}',
		// 		'english_name' => 'Happy Birds',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906525',
		// 		'game_name' => '_json:{"1":"Happy Birds","2":"Happy Birds"}',
		// 		'english_name' => 'Happy Birds',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881582',
		// 		'game_name' => '_json:{"1":"Heavy Metal: Warriors™","2":"Heavy Metal: Warriors™"}',
		// 		'english_name' => 'Heavy Metal: Warriors™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896529',
		// 		'game_name' => '_json:{"1":"Heavy Metal: Warriors™","2":"Heavy Metal: Warriors™"}',
		// 		'english_name' => 'Heavy Metal: Warriors™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881526',
		// 		'game_name' => '_json:{"1":"Illusions 2","2":"Illusions 2"}',
		// 		'english_name' => 'Illusions 2',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906505',
		// 		'game_name' => '_json:{"1":"Illusions 2","2":"Illusions 2"}',
		// 		'english_name' => 'Illusions 2',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => 'unknown',
		// 		'game_name' => '_json:{"1":"isb.unknown","2":"isb.unknown"}',
		// 		'english_name' => 'isb.unknown',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881510',
		// 		'game_name' => '_json:{"1":"Jackpot Rango™","2":"Jackpot Rango™"}',
		// 		'english_name' => 'Jackpot Rango™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906502',
		// 		'game_name' => '_json:{"1":"Jackpot Rango™","2":"Jackpot Rango™"}',
		// 		'english_name' => 'Jackpot Rango™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882007',
		// 		'game_name' => '_json:{"1":"Jacks or Better","2":"Jacks or Better"}',
		// 		'english_name' => 'Jacks or Better',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882008',
		// 		'game_name' => '_json:{"1":"Joker Multitimes","2":"Joker Multitimes"}',
		// 		'english_name' => 'Joker Multitimes',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882009',
		// 		'game_name' => '_json:{"1":"Joker Poker","2":"Joker Poker"}',
		// 		'english_name' => 'Joker Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896521',
		// 		'game_name' => '_json:{"1":"Joker Poker","2":"Joker Poker"}',
		// 		'english_name' => 'Joker Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882015',
		// 		'game_name' => '_json:{"1":"Joker Poker Big Beer","2":"Joker Poker Big Beer"}',
		// 		'english_name' => 'Joker Poker Big Beer',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882010',
		// 		'game_name' => '_json:{"1":"Joker Poker Progressive","2":"Joker Poker Progressive"}',
		// 		'english_name' => 'Joker Poker Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882030',
		// 		'game_name' => '_json:{"1":"Joker Poker VIP","2":"Joker Poker VIP"}',
		// 		'english_name' => 'Joker Poker VIP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882029',
		// 		'game_name' => '_json:{"1":"Joker Vegas 4UP","2":"Joker Vegas 4UP"}',
		// 		'english_name' => 'Joker Vegas 4UP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882012',
		// 		'game_name' => '_json:{"1":"Joker Wheel Bonus","2":"Joker Wheel Bonus"}',
		// 		'english_name' => 'Joker Wheel Bonus',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882013',
		// 		'game_name' => '_json:{"1":"Joker Wild Poker","2":"Joker Wild Poker"}',
		// 		'english_name' => 'Joker Wild Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881517',
		// 		'game_name' => '_json:{"1":"Kobushi™","2":"Kobushi™"}',
		// 		'english_name' => 'Kobushi™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906509',
		// 		'game_name' => '_json:{"1":"Kobushi™","2":"Kobushi™"}',
		// 		'english_name' => 'Kobushi™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881504',
		// 		'game_name' => '_json:{"1":"Lucky Clover","2":"Lucky Clover"}',
		// 		'english_name' => 'Lucky Clover',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896556',
		// 		'game_name' => '_json:{"1":"Lucky Clover","2":"Lucky Clover"}',
		// 		'english_name' => 'Lucky Clover',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881537',
		// 		'game_name' => '_json:{"1":"Lucky Leprechaun","2":"Lucky Leprechaun"}',
		// 		'english_name' => 'Lucky Leprechaun',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896557',
		// 		'game_name' => '_json:{"1":"Lucky Leprechaun","2":"Lucky Leprechaun"}',
		// 		'english_name' => 'Lucky Leprechaun',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881595',
		// 		'game_name' => '_json:{"1":"Luxury Rome","2":"Luxury Rome"}',
		// 		'english_name' => 'Luxury Rome',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '996548',
		// 		'game_name' => '_json:{"1":"Luxury Rome","2":"Luxury Rome"}',
		// 		'english_name' => 'Luxury Rome',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881589',
		// 		'game_name' => '_json:{"1":"MegaBoy","2":"MegaBoy"}',
		// 		'english_name' => 'MegaBoy',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896550',
		// 		'game_name' => '_json:{"1":"MegaBoy","2":"MegaBoy"}',
		// 		'english_name' => 'MegaBoy',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881503',
		// 		'game_name' => '_json:{"1":"Million Cents","2":"Million Cents"}',
		// 		'english_name' => 'Million Cents',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906512',
		// 		'game_name' => '_json:{"1":"Million Cents","2":"Million Cents"}',
		// 		'english_name' => 'Million Cents',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881580',
		// 		'game_name' => '_json:{"1":"Million Cents HD","2":"Million Cents HD"}',
		// 		'english_name' => 'Million Cents HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881511',
		// 		'game_name' => '_json:{"1":"Mona Lisa Jewels","2":"Mona Lisa Jewels"}',
		// 		'english_name' => 'Mona Lisa Jewels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906520',
		// 		'game_name' => '_json:{"1":"Mona Lisa Jewels","2":"Mona Lisa Jewels"}',
		// 		'english_name' => 'Mona Lisa Jewels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881250',
		// 		'game_name' => '_json:{"1":"Musketeer Slot","2":"Musketeer Slot"}',
		// 		'english_name' => 'Musketeer Slot',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896543',
		// 		'game_name' => '_json:{"1":"Musketeer Slot","2":"Musketeer Slot"}',
		// 		'english_name' => 'Musketeer Slot',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881576',
		// 		'game_name' => '_json:{"1":"Nacho Libre™","2":"Nacho Libre™"}',
		// 		'english_name' => 'Nacho Libre™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896535',
		// 		'game_name' => '_json:{"1":"Nacho Libre™","2":"Nacho Libre™"}',
		// 		'english_name' => 'Nacho Libre™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881592',
		// 		'game_name' => '_json:{"1":"Neon Reels","2":"Neon Reels"}',
		// 		'english_name' => 'Neon Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896552',
		// 		'game_name' => '_json:{"1":"Neon Reels","2":"Neon Reels"}',
		// 		'english_name' => 'Neon Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881516',
		// 		'game_name' => '_json:{"1":"Ninja Chef","2":"Ninja Chef"}',
		// 		'english_name' => 'Ninja Chef',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906503',
		// 		'game_name' => '_json:{"1":"Ninja Chef","2":"Ninja Chef"}',
		// 		'english_name' => 'Ninja Chef',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881280',
		// 		'game_name' => '_json:{"1":"Pin Up Girls","2":"Pin Up Girls"}',
		// 		'english_name' => 'Pin Up Girls',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881559',
		// 		'game_name' => '_json:{"1":"Pirates Island","2":"Pirates Island"}',
		// 		'english_name' => 'Pirates Island',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881606',
		// 		'game_name' => '_json:{"1":"Platoon Wild™","2":"Platoon Wild™"}',
		// 		'english_name' => 'Platoon Wild™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906559',
		// 		'game_name' => '_json:{"1":"Platoon Wild™","2":"Platoon Wild™"}',
		// 		'english_name' => 'Platoon Wild™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881605',
		// 		'game_name' => '_json:{"1":"Platoon Wild™Progressive","2":"Platoon Wild™Progressive"}',
		// 		'english_name' => 'Platoon Wild™Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906555',
		// 		'game_name' => '_json:{"1":"Platoon Wild™Progressive","2":"Platoon Wild™Progressive"}',
		// 		'english_name' => 'Platoon Wild™Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881575',
		// 		'game_name' => '_json:{"1":"Platoon™","2":"Platoon™"}',
		// 		'english_name' => 'Platoon™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906510',
		// 		'game_name' => '_json:{"1":"Platoon™","2":"Platoon™"}',
		// 		'english_name' => 'Platoon™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882014',
		// 		'game_name' => '_json:{"1":"Poker Bowling Strike","2":"Poker Bowling Strike"}',
		// 		'english_name' => 'Poker Bowling Strike',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883011',
		// 		'game_name' => '_json:{"1":"Poker Pursuit","2":"Poker Pursuit"}',
		// 		'english_name' => 'Poker Pursuit',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882011',
		// 		'game_name' => '_json:{"1":"Poker Pursuit","2":"Poker Pursuit"}',
		// 		'english_name' => 'Poker Pursuit',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883045',
		// 		'game_name' => '_json:{"1":"Punto Banco","2":"Punto Banco"}',
		// 		'english_name' => 'Punto Banco',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881568',
		// 		'game_name' => '_json:{"1":"Rambo™","2":"Rambo™"}',
		// 		'english_name' => 'Rambo™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896511',
		// 		'game_name' => '_json:{"1":"Rambo™","2":"Rambo™"}',
		// 		'english_name' => 'Rambo™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881550',
		// 		'game_name' => '_json:{"1":"Red Dragon Wild","2":"Red Dragon Wild"}',
		// 		'english_name' => 'Red Dragon Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906514',
		// 		'game_name' => '_json:{"1":"Red Dragon Wild","2":"Red Dragon Wild"}',
		// 		'english_name' => 'Red Dragon Wild',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883032',
		// 		'game_name' => '_json:{"1":"Roulette 3D","2":"Roulette 3D"}',
		// 		'english_name' => 'Roulette 3D',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883028',
		// 		'game_name' => '_json:{"1":"Roulette Silver","2":"Roulette Silver"}',
		// 		'english_name' => 'Roulette Silver',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883027',
		// 		'game_name' => '_json:{"1":"Roulette VIP","2":"Roulette VIP"}',
		// 		'english_name' => 'Roulette VIP',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881549',
		// 		'game_name' => '_json:{"1":"Royal Cash","2":"Royal Cash"}',
		// 		'english_name' => 'Royal Cash',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896530',
		// 		'game_name' => '_json:{"1":"Royal Cash","2":"Royal Cash"}',
		// 		'english_name' => 'Royal Cash',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881585',
		// 		'game_name' => '_json:{"1":"Scrolls of Ra HD","2":"Scrolls of Ra HD"}',
		// 		'english_name' => 'Scrolls of Ra HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906536',
		// 		'game_name' => '_json:{"1":"Scrolls of Ra HD","2":"Scrolls of Ra HD"}',
		// 		'english_name' => 'Scrolls of Ra HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881593',
		// 		'game_name' => '_json:{"1":"Shaolin Spin","2":"Shaolin Spin"}',
		// 		'english_name' => 'Shaolin Spin',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896539',
		// 		'game_name' => '_json:{"1":"Shaolin Spin","2":"Shaolin Spin"}',
		// 		'english_name' => 'Shaolin Spin',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '',
		// 		'game_name' => '_json:{"1":"Skulls of Legend","2":"Skulls of Legend"}',
		// 		'english_name' => 'Skulls of Legend',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '',
		// 		'game_name' => '_json:{"1":"Skulls of Legend","2":"Skulls of Legend"}',
		// 		'english_name' => 'Skulls of Legend',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881539',
		// 		'game_name' => '_json:{"1":"Spin or Reel HD","2":"Spin or Reel HD"}',
		// 		'english_name' => 'Spin or Reel HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896553',
		// 		'game_name' => '_json:{"1":"Spin or Reel HD","2":"Spin or Reel HD"}',
		// 		'english_name' => 'Spin or Reel HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881521',
		// 		'game_name' => '_json:{"1":"Spooky Family","2":"Spooky Family"}',
		// 		'english_name' => 'Spooky Family',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896526',
		// 		'game_name' => '_json:{"1":"Spooky Family","2":"Spooky Family"}',
		// 		'english_name' => 'Spooky Family',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883008',
		// 		'game_name' => '_json:{"1":"Stud Poker","2":"Stud Poker"}',
		// 		'english_name' => 'Stud Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '883049',
		// 		'game_name' => '_json:{"1":"Stud Poker 3D","2":"Stud Poker 3D"}',
		// 		'english_name' => 'Stud Poker 3D',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881506',
		// 		'game_name' => '_json:{"1":"Super Fast Hot Hot","2":"Super Fast Hot Hot"}',
		// 		'english_name' => 'Super Fast Hot Hot',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906516',
		// 		'game_name' => '_json:{"1":"Super Fast Hot Hot","2":"Super Fast Hot Hot"}',
		// 		'english_name' => 'Super Fast Hot Hot',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881535',
		// 		'game_name' => '_json:{"1":"Super Lucky Reels","2":"Super Lucky Reels"}',
		// 		'english_name' => 'Super Lucky Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906501',
		// 		'game_name' => '_json:{"1":"Super Lucky Reels","2":"Super Lucky Reels"}',
		// 		'english_name' => 'Super Lucky Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881501',
		// 		'game_name' => '_json:{"1":"Super Multitimes Progressive","2":"Super Multitimes Progressive"}',
		// 		'english_name' => 'Super Multitimes Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906515',
		// 		'game_name' => '_json:{"1":"Super Multitimes Progressive","2":"Super Multitimes Progressive"}',
		// 		'english_name' => 'Super Multitimes Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881501',
		// 		'game_name' => '_json:{"1":"Super Multitimes Progressive HD","2":"Super Multitimes Progressive HD"}',
		// 		'english_name' => 'Super Multitimes Progressive HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906515',
		// 		'game_name' => '_json:{"1":"Super Multitimes Progressive HD","2":"Super Multitimes Progressive HD"}',
		// 		'english_name' => 'Super Multitimes Progressive HD',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881502',
		// 		'game_name' => '_json:{"1":"Super Tricolor","2":"Super Tricolor"}',
		// 		'english_name' => 'Super Tricolor',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882018',
		// 		'game_name' => '_json:{"1":"Tens or Better Poker","2":"Tens or Better Poker"}',
		// 		'english_name' => 'Tens or Better Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882031',
		// 		'game_name' => '_json:{"1":"Tens or Better Progressive","2":"Tens or Better Progressive"}',
		// 		'english_name' => 'Tens or Better Progressive',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882016',
		// 		'game_name' => '_json:{"1":"Texas Hold’em Joker Poker","2":"Texas Hold’em Joker Poker"}',
		// 		'english_name' => 'Texas Hold’em Joker Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881581',
		// 		'game_name' => '_json:{"1":"The Best Witch","2":"The Best Witch"}',
		// 		'english_name' => 'The Best Witch',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896538',
		// 		'game_name' => '_json:{"1":"The Best Witch","2":"The Best Witch"}',
		// 		'english_name' => 'The Best Witch',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881512',
		// 		'game_name' => '_json:{"1":"The Love Guru™","2":"The Love Guru™"}',
		// 		'english_name' => 'The Love Guru™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896531',
		// 		'game_name' => '_json:{"1":"The Love Guru™","2":"The Love Guru™"}',
		// 		'english_name' => 'The Love Guru™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881538',
		// 		'game_name' => '_json:{"1":"The Olympic Slots","2":"The Olympic Slots"}',
		// 		'english_name' => 'The Olympic Slots',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881577',
		// 		'game_name' => '_json:{"1":"The Warriors™","2":"The Warriors™"}',
		// 		'english_name' => 'The Warriors™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '886537',
		// 		'game_name' => '_json:{"1":"The Warriors™","2":"The Warriors™"}',
		// 		'english_name' => 'The Warriors™',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881557',
		// 		'game_name' => '_json:{"1":"Treasure Chest","2":"Treasure Chest"}',
		// 		'english_name' => 'Treasure Chest',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881536',
		// 		'game_name' => '_json:{"1":"Ultimate Super Reels","2":"Ultimate Super Reels"}',
		// 		'english_name' => 'Ultimate Super Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '906513',
		// 		'game_name' => '_json:{"1":"Ultimate Super Reels","2":"Ultimate Super Reels"}',
		// 		'english_name' => 'Ultimate Super Reels',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '882017',
		// 		'game_name' => '_json:{"1":"Vegas Joker Poker","2":"Vegas Joker Poker"}',
		// 		'english_name' => 'Vegas Joker Poker',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881588',
		// 		'game_name' => '_json:{"1":"Wisps","2":"Wisps"}',
		// 		'english_name' => 'Wisps',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '896547',
		// 		'game_name' => '_json:{"1":"Wisps","2":"Wisps"}',
		// 		'english_name' => 'Wisps',
		// 	),
		// 	array(
		// 		'game_platform_id' => ISB_API,
		// 		'game_code' => '881563',
		// 		'game_name' => '_json:{"1":"World Tour","2":"World Tour"}',
		// 		'english_name' => 'World Tour',
		// 	),
		// );

		// $data = array();

		// foreach ($game_descriptions as $game_list) {

		// 	$game_code_exist = $this->db->select('COUNT(1) as count')
		// 					 	->where('game_code', $game_list['game_code'])
		// 					 	->where('game_platform_id', ISB_API)
		// 					 	->get('game_description')
		// 			 		 	->row();

		// 	if( $game_code_exist->count <= 0 ) continue;

		// 	$this->db->where('game_code', $game_list['game_code']);
		// 	$this->db->where('game_platform_id', ISB_API);
		// 	$this->db->update('game_description', $game_list);

		// }

		// $this->db->trans_complete();

	}
	public function down() {

		// $this->db->trans_start();


		// $this->db->trans_complete();

	}
}
