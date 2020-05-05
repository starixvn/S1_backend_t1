<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gameplay_in_game_type_and_game_description_201603230307 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $this->db->trans_start();

		// $where = "game_platform_id = '" . GAMEPLAY_API . "'";
		// $this->db->where($where);
		// $this->db->delete('game_type');

		// $where = "game_platform_id = '" . GAMEPLAY_API . "'";
		// $this->db->where($where);
		// $this->db->delete('game_description');

		// //insert to game_description
		// $data = array(
		// 	array(
		// 		'game_type' => 'Slot',
		// 		'game_type_lang' => 'gameplay_slots',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array('game_name' => 'gameplay.baseball',
		// 				'english_name' => 'Baseball',
		// 				'external_game_id' => 'baseball',
		// 				'game_code' => 'baseball',
		// 			),

		// 			array('game_name' => 'gameplay.games.casinoroyale',
		// 				'english_name' => 'Casino Royale',
		// 				'external_game_id' => 'casinoroyale',
		// 				'game_code' => 'casinoroyale',
		// 			),

		// 			array('game_name' => 'gameplay.underwaterworld',
		// 				'english_name' => 'Under Water World',
		// 				'external_game_id' => 'underwaterworld',
		// 				'game_code' => 'underwaterworld',
		// 			),

		// 			array('game_name' => 'gameplay.mafia',
		// 				'english_name' => 'Mafia',
		// 				'external_game_id' => 'mafia',
		// 				'game_code' => 'mafia',
		// 			),

		// 			array('game_name' => 'gameplay.romanempire',
		// 				'english_name' => 'Roman Empire',
		// 				'external_game_id' => 'romanempire',
		// 				'game_code' => 'romanempire',
		// 			),

		// 			array('game_name' => 'gameplay.nightclub',
		// 				'english_name' => 'Night Club',
		// 				'external_game_id' => 'nightclub',
		// 				'game_code' => 'nightclub',
		// 			),

		// 			array('game_name' => 'gameplay.wildwildwestern',
		// 				'english_name' => 'Wild Wild Western',
		// 				'external_game_id' => 'wildwildwestern',
		// 				'game_code' => 'wildwildwestern',
		// 			),

		// 			array('game_name' => 'gameplay.boxing',
		// 				'english_name' => 'Boxing',
		// 				'external_game_id' => 'boxing',
		// 				'game_code' => 'boxing',
		// 			),

		// 			array('game_name' => 'gameplay.soccer',
		// 				'english_name' => 'Soccer',
		// 				'external_game_id' => 'soccer',
		// 				'game_code' => 'soccer',
		// 			),

		// 			array('game_name' => 'gameplay.kpop',
		// 				'english_name' => 'K-Pop',
		// 				'external_game_id' => 'kpop',
		// 				'game_code' => 'kpop',
		// 			),

		// 			array('game_name' => 'gameplay.redchamber',
		// 				'english_name' => 'The Red Chamber',
		// 				'external_game_id' => 'redchamber',
		// 				'game_code' => 'redchamber',
		// 			),

		// 			array('game_name' => 'gameplay.blackjack',
		// 				'english_name' => 'Blackjack',
		// 				'external_game_id' => 'blackjack',
		// 				'game_code' => 'blackjack',
		// 			),

		// 			array('game_name' => 'gameplay.games.987',
		// 				'english_name' => 'Lady Luck',
		// 				'external_game_id' => 'ladyluck',
		// 				'game_code' => 'ladyluck',
		// 			),

		// 			array('game_name' => 'gameplay.forbiddenchamber',
		// 				'english_name' => 'The Forbidden Chamber',
		// 				'external_game_id' => 'forbiddenchamber',
		// 				'game_code' => 'forbiddenchamber',
		// 			),

		// 			array('game_name' => 'gameplay.littlemonsters',
		// 				'english_name' => 'Little Monsters',
		// 				'external_game_id' => 'littlemonsters',
		// 				'game_code' => 'littlemonsters',
		// 			),

		// 			array('game_name' => 'gameplay.fruitilicious',
		// 				'english_name' => 'Fruitilicious',
		// 				'external_game_id' => 'fruitilicious',
		// 				'game_code' => 'fruitilicious',
		// 			),

		// 			array('game_name' => 'gameplay.freedomfighter',
		// 				'english_name' => 'Freedom Fighter',
		// 				'external_game_id' => 'freedomfighter',
		// 				'game_code' => 'freedomfighter',
		// 			),

		// 			array('game_name' => 'gameplay.threekingdoms_hd',
		// 				'english_name' => 'Three Kingdom HD',
		// 				'external_game_id' => 'threekingdoms_hd',
		// 				'game_code' => 'threekingdoms_hd',
		// 			),

		// 			array('game_name' => 'gameplay.bikinibeach_hd',
		// 				'english_name' => 'Bikini Beach HD',
		// 				'external_game_id' => 'bikinibeach_hd',
		// 				'game_code' => 'bikinibeach_hd',
		// 			),

		// 			array('game_name' => 'gameplay.monkeyking_hd',
		// 				'english_name' => 'The Monkey King HD',
		// 				'external_game_id' => 'monkeyking_hd',
		// 				'game_code' => 'monkeyking_hd',
		// 			),

		// 			array('game_name' => 'gameplay.ninetailedninja',
		// 				'english_name' => 'Nine Tailed Ninja',
		// 				'external_game_id' => 'ninetailedninja',
		// 				'game_code' => 'ninetailedninja',
		// 			),

		// 			array('game_name' => 'gameplay.trickortreat',
		// 				'english_name' => 'Trick or Treat',
		// 				'external_game_id' => 'trickortreat',
		// 				'game_code' => 'trickortreat',
		// 			),

		// 			array('game_name' => 'gameplay.desertoasis',
		// 				'english_name' => 'Desert Oasis',
		// 				'external_game_id' => 'desertoasis',
		// 				'game_code' => 'desertoasis',
		// 			),

		// 			array('game_name' => 'gameplay.queenbee',
		// 				'english_name' => 'Queen Bee',
		// 				'external_game_id' => 'queenbee',
		// 				'game_code' => 'queenbee',
		// 			),

		// 			array('game_name' => 'gameplay.nutcracker',
		// 				'english_name' => 'The Nutcracker',
		// 				'external_game_id' => 'nutcracker',
		// 				'game_code' => 'nutcracker',
		// 			),

		// 			array('game_name' => 'gameplay.winterwonderland',
		// 				'english_name' => 'Winter Wonderland',
		// 				'external_game_id' => 'winterwonderland',
		// 				'game_code' => 'winterwonderland',
		// 			),

		// 			array('game_name' => 'gameplay.candylicious',
		// 				'english_name' => 'Candylicious',
		// 				'external_game_id' => 'candylicious',
		// 				'game_code' => 'candylicious',
		// 			),

		// 			array('game_name' => 'gameplay.florasecret',
		// 				'english_name' => 'Flora\'s Secret',
		// 				'external_game_id' => 'florasecret',
		// 				'game_code' => 'florasecret',
		// 			),

		// 			array('game_name' => 'gameplay.sherlock',
		// 				'english_name' => 'Sherlock',
		// 				'external_game_id' => 'sherlock',
		// 				'game_code' => 'sherlock',
		// 			),

		// 			array('game_name' => 'gameplay.godoffortune',
		// 				'english_name' => 'God of Fortune',
		// 				'external_game_id' => 'godoffortune',
		// 				'game_code' => 'godoffortune',
		// 			),

		// 			array('game_name' => 'gameplay.kingsofhighway',
		// 				'english_name' => 'Kings of Highway',
		// 				'external_game_id' => 'kingsofhighway',
		// 				'game_code' => 'kingsofhighway',
		// 			),

		// 			array('game_name' => 'gameplay.klassik',
		// 				'english_name' => 'KlassiK',
		// 				'external_game_id' => 'klassik',
		// 				'game_code' => 'klassik',
		// 			),

		// 			array('game_name' => 'gameplay.magicquest',
		// 				'english_name' => 'Magic Quest',
		// 				'external_game_id' => 'magicquest',
		// 				'game_code' => 'magicquest',
		// 			),

		// 			array('game_name' => 'gameplay.panda',
		// 				'english_name' => 'Panda',
		// 				'external_game_id' => 'panda',
		// 				'game_code' => 'panda',
		// 			),

		// 			array('game_name' => 'gameplay.piratestreasure',
		// 				'english_name' => 'Pirate\'s Treasure',
		// 				'external_game_id' => 'piratestreasure',
		// 				'game_code' => 'piratestreasure',
		// 			),

		// 			array(
		// 				'game_name' => 'gameplay.zodiac',
		// 				'english_name' => 'Zodiac',
		// 				'external_game_id' => 'zodiac',
		// 				'game_code' => 'zodiac',
		// 			),

		// 			array(
		// 				'game_name' => 'gameplay.streetbasketball',
		// 				'english_name' => 'Street Basketball',
		// 				'external_game_id' => 'streetbasketball',
		// 				'game_code' => 'streetbasketball',
		// 			),

		// 			array(
		// 				'game_name' => 'gameplay.golftour',
		// 				'english_name' => 'Golf Tour',
		// 				'external_game_id' => 'golftour',
		// 				'game_code' => 'golftour',
		// 			),

		// 			array('game_name' => 'gameplay.4beauties',
		// 				'english_name' => 'Four Beauties',
		// 				'external_game_id' => '4beauties',
		// 				'game_code' => '4beauties',
		// 			),

		// 			array('game_name' => 'gameplay.skystrikers',
		// 				'english_name' => 'Sky Strikers',
		// 				'external_game_id' => 'skystrikers',
		// 				'game_code' => 'skystrikers',
		// 			),
		// 		),
		// 	),

		// 	array(
		// 		'game_type' => 'Table',
		// 		'game_type_lang' => 'gameplay_table',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array('game_name' => 'gameplay.games.1',
		// 				'english_name' => 'Commision Baccarat 1',
		// 				'external_game_id' => '1',
		// 				'game_code' => '1',
		// 			),

		// 			array('game_name' => 'gameplay.games.2',
		// 				'english_name' => 'Commision Baccarat 2',
		// 				'external_game_id' => '2',
		// 				'game_code' => '2',
		// 			),

		// 			array('game_name' => 'gameplay.games.3',
		// 				'english_name' => 'Commision Baccarat 3',
		// 				'external_game_id' => '3',
		// 				'game_code' => '3',
		// 			),

		// 			array('game_name' => 'gameplay.games.101',
		// 				'english_name' => 'NC Baccarat 1',
		// 				'external_game_id' => '101',
		// 				'game_code' => '101',
		// 			),

		// 			array('game_name' => 'gameplay.games.102',
		// 				'english_name' => 'NC Baccarat 2',
		// 				'external_game_id' => '101',
		// 				'game_code' => '101',
		// 			),

		// 			array('game_name' => 'gameplay.games.103',
		// 				'english_name' => 'NC Baccarat 3',
		// 				'external_game_id' => '103',
		// 				'game_code' => '103',
		// 			),

		// 			array('game_name' => 'gameplay.games.4',
		// 				'english_name' => 'Dragon Tiger',
		// 				'external_game_id' => '4',
		// 				'game_code' => '4',
		// 			),

		// 			array('game_name' => 'gameplay.games.5',
		// 				'english_name' => 'Sicbo ',
		// 				'external_game_id' => '5',
		// 				'game_code' => '5',
		// 			),

		// 			array('game_name' => 'gameplay.games.6',
		// 				'english_name' => 'Roulette ',
		// 				'external_game_id' => '6',
		// 				'game_code' => '6',
		// 			),

		// 			array('game_name' => 'gameplay.games.7',
		// 				'english_name' => 'Seven Up Baccarat',
		// 				'external_game_id' => '7',
		// 				'game_code' => '7',
		// 			),

		// 			array('game_name' => 'gameplay.games.8',
		// 				'english_name' => '3 Pictures',
		// 				'external_game_id' => '8',
		// 				'game_code' => '8',
		// 			),

		// 			array('game_name' => 'gameplay.games.9',
		// 				'english_name' => 'Super Color Sicbo',
		// 				'external_game_id' => '9',
		// 				'game_code' => '9',
		// 			),

		// 			array('game_name' => 'gameplay.games.10',
		// 				'english_name' => 'Blackjack ',
		// 				'external_game_id' => '10',
		// 				'game_code' => '10',
		// 			),

		// 			array('game_name' => 'gameplay.games.11',
		// 				'english_name' => 'Tambola',
		// 				'external_game_id' => '11',
		// 				'game_code' => '11',
		// 			),

		// 			array('game_name' => 'gameplay.games.12',
		// 				'english_name' => 'Super Fan Tan',
		// 				'external_game_id' => '12',
		// 				'game_code' => '12',
		// 			),
		// 		),
		// 	),

		// 	array(
		// 		'game_type' => 'unknown',
		// 		'game_type_lang' => 'gameplay.unknown',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_FALSE,
		// 		'game_description_list' => array(
		// 			array('game_name' => 'gameplay.unknown',
		// 				'english_name' => 'Unknown Gameplay Game',
		// 				'external_game_id' => 'unknown',
		// 				'game_code' => 'unknown',
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