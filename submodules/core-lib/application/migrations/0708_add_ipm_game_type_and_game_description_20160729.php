<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ipm_game_type_and_game_description_20160729 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		// $this->db->trans_start();

		// $game_types = array(
		// 	'Soccer',
		// 	'Basketball',
		// 	'Tennis',
		// 	'Motor Racing',
		// 	'Golf',
		// 	'Soccer (HT)',
		// 	'Football',
		// 	'Hockey',
		// 	'Baseball',
		// 	'Volleyball',
		// 	'Badminton',
		// 	'Snooker',
		// 	'Boxing',
		// 	'Rugby',
		// 	'Cricket',
		// 	'Handball',
		// 	'FinancialBets',
		// 	'Futsal',
		// 	'Asian9Ball',
		// 	'Billiard',
		// 	'Darts',
		// 	'WaterPolo',
		// 	'Olympic',
		// 	'Cycling',
		// 	'Beach Volleyball',
		// 	'Field Hockey',
		// 	'Table Tennis',
		// 	'Athletics',
		// 	'Archery',
		// 	'Weight Lifting',
		// 	'Canoeing',
		// 	'Gymnastics',
		// 	'Equestrian',
		// 	'Triathlon',
		// 	'Swimming',
		// 	'Fencing',
		// 	'Judo',
		// 	'M. Pentathlon',
		// 	'Rowing',
		// 	'Sailing',
		// 	'Shooting',
		// 	'Taekwondo',
		// 	'Virtual Soccer',
		// 	'Virtual Basketball',
		// );

		// $game_descriptions = array(
		// 	array('code' => '1stCS', 'name' => '1H – Correct Score'),
		// 	array('code' => '1stDC', 'name' => '1H – Double Chance'),
		// 	array('code' => '1stFTLT', 'name' => '1H – Team to Score'),
		// 	array('code' => '1STHALF1x2', 'name' => '1H – 1x2'),
		// 	array('code' => '1STHALFAH', 'name' => '1H – Handicap'),
		// 	array('code' => '1STHALFOU', 'name' => '1H – Over / Under'),
		// 	array('code' => '1STHFRB', 'name' => '1H – RB handicap'),
		// 	array('code' => '1STHFRB1x2', 'name' => '1H – RB 1x2'),
		// 	array('code' => '1STHFRBOU', 'name' => '1H – RB Over / Under'),
		// 	array('code' => '1stOE', 'name' => '1H – Odd / Even'),
		// 	array('code' => '1stTG', 'name' => '1H – Total Goal'),
		// 	array('code' => '1x2', 'name' => 'FT – 1x2'),
		// 	array('code' => '2nd1x2', 'name' => '2H – 1x2'),
		// 	array('code' => '2ndAH', 'name' => '2H – Handicap'),
		// 	array('code' => '2ndCS', 'name' => '2H – Correct Score'),
		// 	array('code' => '2ndDC', 'name' => '2H – Double Chance'),
		// 	array('code' => '2ndFTLT', 'name' => '2H – Team to Score'),
		// 	array('code' => '2ndOE', 'name' => '2H – Odd / Even'),
		// 	array('code' => '2ndOU', 'name' => '2H – Over / Under'),
		// 	array('code' => '2ndTG', 'name' => '2H – Total Goal'),
		// 	array('code' => 'AH', 'name' => 'FT – Handicap'),
		// 	array('code' => 'CS', 'name' => 'FT – Correct Score'),
		// 	array('code' => 'HF', 'name' => 'FT – Double Chance'),
		// 	array('code' => 'OE', 'name' => 'FT – Halftime / Fulltime'),
		// 	array('code' => 'OR', 'name' => 'FT – Odd / Even'),
		// 	array('code' => 'OU', 'name' => 'Combo'),
		// 	array('code' => 'PARLAYALL', 'name' => 'FT – Over / Under'),
		// 	array('code' => 'RB', 'name' => 'FT – RB Handicap'),
		// 	array('code' => 'RB1stCS', 'name' => '1H – RB Correct Score'),
		// 	array('code' => 'RB1stDC', 'name' => '1H – RB Double Chance'),
		// 	array('code' => 'RB1stFTLT', 'name' => '1H – RB Team to Score'),
		// 	array('code' => 'RB1stOE', 'name' => '1H – RB Odd / Even'),
		// 	array('code' => 'RB1stTG', 'name' => '1H – RB Total Goal'),
		// 	array('code' => 'RB1x2', 'name' => 'FT – RB 1x2'),
		// 	array('code' => 'RB2nd1x2', 'name' => '2H – RB 1x2'),
		// 	array('code' => 'RB2ndAH', 'name' => '2H – RB Handicap'),
		// 	array('code' => 'RB2ndCS', 'name' => '2H – RB Correct Score'),
		// 	array('code' => 'RB2ndDC', 'name' => '2H – RB Double Chance'),
		// 	array('code' => 'RB2ndFTLT', 'name' => '2H – RB Team to Score'),
		// 	array('code' => 'RB2ndOE', 'name' => '2H – RB Odd / Even'),
		// 	array('code' => 'RB2ndOU', 'name' => '2H – RB Over / Under'),
		// 	array('code' => 'RB2ndTG', 'name' => '2H – RB Total Goal'),
		// 	array('code' => 'RBCS', 'name' => 'FT – RB Correct Score'),
		// 	array('code' => 'RBDC', 'name' => 'FT – RB Double Chance'),
		// 	array('code' => 'RBFTLT', 'name' => 'FT – RB Team to Score'),
		// 	array('code' => 'RBHF', 'name' => 'FT – RB Halftime / Fulltime'),
		// 	array('code' => 'RBOE', 'name' => 'FT – RB Odd / Even'),
		// 	array('code' => 'RBOU', 'name' => 'FT – RB Over / Under'),
		// 	array('code' => 'RBTG', 'name' => 'FT – RB Total Goal'),
		// 	array('code' => 'TG', 'name' => 'FT – Total Goal'),
		// 	array('code' => 'TMSCO1ST', 'name' => 'FT – Team to Score'),
		// );

		// foreach ($game_types as $game_type) {

		// 	$this->db->insert('game_type', array(
		// 		'game_platform_id' => SPORTSBOOK_API,
		// 		'game_type' => $game_type,
		// 		'game_type_lang' => $game_type,
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 	));

		// 	$game_type_id = $this->db->insert_id();

		// 	$game_description_batch = array_map( function($game_description) use ($game_type_id, $game_type) {
		// 		return array(
		// 			'game_platform_id' => SPORTSBOOK_API,
		// 			'game_type_id' => $game_type_id,
		// 			'game_name' => $game_description['name'],
		// 			'english_name' => $game_description['name'],
		// 			'html_five_enabled' => self::FLAG_FALSE,
		// 			'flash_enabled' => self::FLAG_TRUE,
		// 			'mobile_enabled' => self::FLAG_FALSE,
		// 			'game_code' => $game_type . $game_description['code'],
		// 			'external_game_id' => $game_description['code'],
		// 		);
		// 	}, $game_descriptions);

		// 	$this->db->insert_batch('game_description', $game_description_batch);

		// }

		// $this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => SPORTSBOOK_API, 'game_type !=' => 'unknown'));
		// $this->db->delete('game_description', array('game_platform_id' => SPORTSBOOK_API, 'game_code !=' => 'unknown'));
		// $this->db->trans_complete();
	}
}