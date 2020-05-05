<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_qt_to_game_type_and_game_description_201604280810 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(
// 			array(
// 					'game_type' => 'Slot',
// 					'game_type_lang' => 'qt_slot_games',
// 					'status' => self::FLAG_TRUE,
// 					'flag_show_in_site' => self::FLAG_TRUE,
// 					'game_description_list' => array(
// 						array(
// 							'game_name' => '1 Can 2 Can',
// 							'game_code' => 'OGS-1can2can',
// 							'english_name' => '1 Can 2 Can',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-1can2can'
// 						),
// 						array(
// 							'game_name' => '300 Shields',
// 							'game_code' => 'OGS-300shields',
// 							'english_name' => '300 Shields',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-300shields'
// 						),
// 						array(
// 							'game_name' => '5 Knights',
// 							'game_code' => 'OGS-5knights',
// 							'english_name' => '5 Knights',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-5knights'
// 						),
// 						array(
// 							'game_name' => 'A Dragon\'s Story',
// 							'game_code' => 'OGS-adragonstory',
// 							'english_name' => 'A Dragon\'s Story',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-adragonstory'
// 						),
// 						array(
// 							'game_name' => 'A While on the Nile',
// 							'game_code' => 'OGS-awhileonthenile',
// 							'english_name' => 'A While on the Nile',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-awhileonthenile'
// 						),
// 						array(
// 							'game_name' => 'Andre the Giant',
// 							'game_code' => 'OGS-andrethegiant',
// 							'english_name' => 'Andre the Giant',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-andrethegiant'
// 						),
// 						array(
// 							'game_name' => 'An Evening with Holly Madison',
// 							'game_code' => 'OGS-aneveningwithhollymadison',
// 							'english_name' => 'An Evening with Holly Madison',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-aneveningwithhollymadison'
// 						),
// 						array(
// 							'game_name' => 'Bangkok Nights',
// 							'game_code' => 'OGS-bangkoknights',
// 							'english_name' => 'Bangkok Nights',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-bangkoknights'
// 						),
// 						array(
// 							'game_name' => 'Big Foot',
// 							'game_code' => 'OGS-bigfoot',
// 							'english_name' => 'Big Foot',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-bigfoot'
// 						),
// 						array(
// 							'game_name' => 'Big Foot Mini',
// 							'game_code' => 'OGS-bigfootmini',
// 							'english_name' => 'Big Foot Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-bigfootmini'
// 						),
// 						array(
// 							'game_name' => 'Bingo Billions',
// 							'game_code' => 'OGS-bingobillions',
// 							'english_name' => 'Bingo Billions',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-bingobillions'
// 						),
// 						array(
// 							'game_name' => 'Bobby 7s',
// 							'game_code' => 'OGS-bobby7s',
// 							'english_name' => 'Bobby 7s',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-bobby7s'
// 						),
// 						array(
// 							'game_name' => 'Bobby 7s Mini',
// 							'game_code' => 'OGS-bobby7smini',
// 							'english_name' => 'Bobby 7s Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-bobby7smini'
// 						),
// 						array(
// 							'game_name' => 'Butterflies',
// 							'game_code' => 'OGS-butterflies',
// 							'english_name' => 'Butterflies',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-butterflies'
// 						),
// 						array(
// 							'game_name' => 'California Gold',
// 							'game_code' => 'OGS-californiagold',
// 							'english_name' => 'California Gold',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-californiagold'
// 						),
// 						array(
// 							'game_name' => 'Call of the Colosseum',
// 							'game_code' => 'OGS-callofthecolosseum',
// 							'english_name' => 'Call of the Colosseum',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-callofthecolosseum'
// 						),
// 						array(
// 							'game_name' => 'Cash Stampede',
// 							'game_code' => 'OGS-cashstampede',
// 							'english_name' => 'Cash Stampede',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-cashstampede'
// 						),
// 						array(
// 							'game_name' => 'Casinomeister',
// 							'game_code' => 'OGS-casinomeister',
// 							'english_name' => 'Casinomeister',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-casinomeister'
// 						),
// 						array(
// 							'game_name' => 'Cherry Blossoms',
// 							'game_code' => 'OGS-cherryblossoms',
// 							'english_name' => 'Cherry Blossoms',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-cherryblossoms'
// 						),
// 						array(
// 							'game_name' => 'Cherry Blossoms Mini',
// 							'game_code' => 'OGS-cherryblossomsmini',
// 							'english_name' => 'Cherry Blossoms Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-cherryblossomsmini'
// 						),
// 						array(
// 							'game_name' => 'Cherry Blossoms Mini',
// 							'game_code' => 'OGS-cherryblossomsmini',
// 							'english_name' => 'Cherry Blossoms Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-cherryblossomsmini'
// 						),
// 						array(
// 							'game_name' => 'Crocodopolis',
// 							'game_code' => 'OGS-crocodopolis',
// 							'english_name' => 'Crocodopolis',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-crocodopolis'
// 						),
// 						array(
// 							'game_name' => 'Doctor Love',
// 							'game_code' => 'OGS-doctorlove',
// 							'english_name' => 'Doctor Love',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-doctorlove'
// 						),
// 						array(
// 							'game_name' => 'Doctor Love Mini',
// 							'game_code' => 'OGS-doctorlovemini',
// 							'english_name' => 'Doctor Love Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-doctorlovemini'
// 						),
// 						array(
// 							'game_name' => 'Dolphin Reef',
// 							'game_code' => 'OGS-dolphinreef',
// 							'english_name' => 'Dolphin Reef',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-dolphinreef'
// 						),
// 						array(
// 							'game_name' => 'Double Play SuperBet',
// 							'game_code' => 'OGS-doubleplaysuperbet',
// 							'english_name' => 'Double Play SuperBet',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-doubleplaysuperbet'
// 						),
// 						array(
// 							'game_name' => 'Dr Love on Vacation',
// 							'game_code' => 'OGS-drloveonvacationt',
// 							'english_name' => 'Dr Love on Vacation',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-drloveonvacation'
// 						),
// 						array(
// 							'game_name' => 'Dragon Drop',
// 							'game_code' => 'OGS-dragondrop',
// 							'english_name' => 'Dragon Drop',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-dragondrop'
// 						),
// 						array(
// 							'game_name' => 'Dynasty',
// 							'game_code' => 'OGS-dynasty',
// 							'english_name' => 'Dynasty',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-dynasty'
// 						),
// 						array(
// 							'game_name' => 'Eastern Dragon',
// 							'game_code' => 'OGS-easterndragon',
// 							'english_name' => 'Eastern Dragon',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-easterndragon'
// 						),
// 						array(
// 							'game_name' => 'Easy Slider',
// 							'game_code' => 'OGS-easyslider',
// 							'english_name' => 'Easy Slider',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-easyslider'
// 						),
// 						array(
// 							'game_name' => 'Emperor\'s Garden',
// 							'game_code' => 'OGS-emperorsgarden',
// 							'english_name' => 'Emperor\'s Garden',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-emperorsgarden'
// 						),
// 						array(
// 							'game_name' => 'Enchanted Mermaid',
// 							'game_code' => 'OGS-enchantedmermaid',
// 							'english_name' => 'Enchanted Mermaid',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-enchantedmermaid'
// 						),
// 						array(
// 							'game_name' => 'Extra Cash!!',
// 							'game_code' => 'OGS-extracash',
// 							'english_name' => 'Extra Cash!!',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-extracash'
// 						),
// 						array(
// 							'game_name' => 'Fairies Forest',
// 							'game_code' => 'OGS-fairiesforest',
// 							'english_name' => 'Fairies Forest',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-fairiesforest'
// 						),
// 						array(
// 							'game_name' => 'Fire Hawk',
// 							'game_code' => 'OGS-firehawk',
// 							'english_name' => 'Fire Hawk',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-firehawk'
// 						),
// 						array(
// 							'game_name' => 'Foxin Wins',
// 							'game_code' => 'OGS-foxinwins',
// 							'english_name' => 'Foxin Wins',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-foxinwins'
// 						),
// 						array(
// 							'game_name' => 'Foxin\' Wins Again',
// 							'game_code' => 'OGS-foxinwinsagain',
// 							'english_name' => 'Foxin\' Wins Again',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-foxinwinsagain'
// 						),
// 						array(
// 							'game_name' => 'Genie Wild',
// 							'game_code' => 'OGS-geniewild',
// 							'english_name' => 'Genie Wild',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'external_game_id' => 'OGS-geniewild'
// 						),
// 						array(
// 							'game_name' => 'Gold Ahoy',
// 							'game_code' => 'OGS-goldahoy',
// 							'english_name' => 'Gold Ahoy',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'external_game_id' => 'OGS-goldahoy'
// 						),
// 						array(
// 							'game_name' => 'Gorilla Go Wild',
// 							'english_name' => 'Gorilla Go Wild',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-gorillagowild',
// 							'external_game_id' => 'OGS-gorillagowild'
// 						),
// 						array(
// 							'game_name' => 'Hot Roller',
// 							'english_name' => 'Hot Roller',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-hotroller',
// 							'external_game_id' => 'OGS-hotroller'
// 						),
// 						array(
// 							'game_name' => 'Irish Eyes',
// 							'english_name' => 'Irish Eyes',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-irisheyes',
// 							'external_game_id' => 'OGS-irisheyes'
// 						),
// 						array(
// 							'game_name' => 'Irish Eyes 2',
// 							'english_name' => 'Irish Eyes 2',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-irisheyes2',
// 							'external_game_id' => 'OGS-irisheyes2'
// 						),
// 						array(
// 							'game_name' => 'Irish Eyes Mini',
// 							'english_name' => 'Irish Eyes Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-irisheyesmini',
// 							'external_game_id' => 'OGS-irisheyesmini'
// 						),
// 						array(
// 							'game_name' => 'Jackpot Jester 50k',
// 							'english_name' => 'Jackpot Jester 50k',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-jackpotjester50k',
// 							'external_game_id' => 'OGS-jackpotjester50k'
// 						),
// 						array(
// 							'game_name' => 'Jackpot Jester Wild Nudge',
// 							'english_name' => 'Jackpot Jester Wild Nudge',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-jackpotjesterwildnudge',
// 							'external_game_id' => 'OGS-jackpotjesterwildnudge'
// 						),
// 						array(
// 							'game_name' => 'James Dean',
// 							'english_name' => 'James Dean',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-jamesdean',
// 							'external_game_id' => 'OGS-jamesdean'
// 						),
// 						array(
// 							'game_name' => 'Joker Jester',
// 							'english_name' => 'Joker Jester',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-jokerjester',
// 							'external_game_id' => 'OGS-jokerjester'
// 						),
// 						array(
// 							'game_name' => 'Joker Jester Mini',
// 							'english_name' => 'Joker Jester Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-jokerjestermini',
// 							'external_game_id' => 'OGS-jokerjestermini'
// 						),
// 						array(
// 							'game_name' => 'Judge Dredd',
// 							'english_name' => 'Judge Dredd',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-judgedredd',
// 							'external_game_id' => 'OGS-judgedredd'
// 						),
// 						array(
// 							'game_name' => 'Jukepot',
// 							'english_name' => 'Jukepot',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-jukepot',
// 							'external_game_id' => 'OGS-jukepot'
// 						),
// 						array(
// 							'game_name' => 'King Tiger',
// 							'english_name' => 'King Tiger',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-kingtiger',
// 							'external_game_id' => 'OGS-kingtiger'
// 						),
// 						array(
// 							'game_name' => 'La Cucaracha',
// 							'english_name' => 'La Cucaracha',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-lacucaracha',
// 							'external_game_id' => 'OGS-lacucaracha'
// 						),
// 						array(
// 							'game_name' => 'Love Bugs',
// 							'english_name' => 'Love Bugs',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-lovebugs',
// 							'external_game_id' => 'OGS-lovebugs'
// 						),
// 						array(
// 							'game_name' => 'Love Bugs Mini',
// 							'english_name' => 'Love Bugs Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-lovebugsmini',
// 							'external_game_id' => 'OGS-lovebugsmini'
// 						),
// 						array(
// 							'game_name' => 'Mad Mad Monkey',
// 							'english_name' => 'Mad Mad Monkey',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-madmadmonkey',
// 							'external_game_id' => 'OGS-madmadmonkey'
// 						),
// 						array(
// 							'game_name' => 'Mad Mad Monkey Mini',
// 							'english_name' => 'Mad Mad Monkey Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-madmadmonkeymini',
// 							'external_game_id' => 'OGS-madmadmonkeymini'
// 						),
// 						array(
// 							'game_name' => 'Maid O Money',
// 							'english_name' => 'Maid O Money',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-maidomoney',
// 							'external_game_id' => 'OGS-maidomoney'
// 						),
// 						array(
// 							'game_name' => 'Medusa',
// 							'english_name' => 'Medusa',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-medusa',
// 							'external_game_id' => 'OGS-medusa'
// 						),
// 						array(
// 							'game_name' => 'Medusa II',
// 							'english_name' => 'Medusa II',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-medusaii',
// 							'external_game_id' => 'OGS-medusaii'
// 						),
// 						array(
// 							'game_name' => 'Medusa Mini',
// 							'english_name' => 'Medusa Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-medusamini',
// 							'external_game_id' => 'OGS-medusamini'
// 						),
// 						array(
// 							'game_name' => 'Merlin\'s Magic Respins',
// 							'english_name' => 'Merlin\'s Magic Respins',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-merlinsmagicrespins',
// 							'external_game_id' => 'OGS-merlinsmagicrespins'
// 						),
// 						array(
// 							'game_name' => 'Merlins Magic Respins Christmas',
// 							'english_name' => 'Merlins Magic Respins Christmas',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-merlinsmagicrespinschristmas',
// 							'external_game_id' => 'OGS-merlinsmagicrespinschristmas'
// 						),
// 						array(
// 							'game_name' => 'Merlin\'s Millions Superbet',
// 							'english_name' => 'Merlin\'s Millions Superbet',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-merlinsmillionssuperbet',
// 							'external_game_id' => 'OGS-merlinsmillionssuperbet'
// 						),
// 						array(
// 							'game_name' => 'Merlin\'s Millions Superbet Mini',
// 							'english_name' => 'Merlin\'s Millions Superbet Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-merlinsmillionssuperbetmini',
// 							'external_game_id' => 'OGS-merlinsmillionssuperbetmini'
// 						),
// 						array(
// 							'game_name' => 'Miss Midas',
// 							'english_name' => 'Miss Midas',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-missmidas',
// 							'external_game_id' => 'OGS-missmidas'
// 						),
// 						array(
// 							'game_name' => 'Munchers',
// 							'english_name' => 'Munchers',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-munchers',
// 							'external_game_id' => 'OGS-munchers'
// 						),
// 						array(
// 							'game_name' => 'Munchers Mini',
// 							'english_name' => 'Munchers Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-munchersmini',
// 							'external_game_id' => 'OGS-munchersmini'
// 						),
// 						array(
// 							'game_name' => 'Napoleon Boney Parts',
// 							'english_name' => 'Napoleon Boney Parts',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-napoleonboneyparts',
// 							'external_game_id' => 'OGS-napoleonboneyparts'
// 						),
// 						array(
// 							'game_name' => 'Oil Mania',
// 							'english_name' => 'Oil Mania',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-oilmania',
// 							'external_game_id' => 'OGS-oilmania'
// 						),
// 						array(
// 							'game_name' => 'Oil Mania Mini',
// 							'english_name' => 'Oil Mania Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-oilmaniamini',
// 							'external_game_id' => 'OGS-oilmaniamini'
// 						),
// 						array(
// 							'game_name' => 'Owl Eyes',
// 							'english_name' => 'Owl Eyes',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-owleyes',
// 							'external_game_id' => 'OGS-owleyes'
// 						),
// 						array(
// 							'game_name' => 'Pandamania',
// 							'english_name' => 'Pandamania',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-pandamania',
// 							'external_game_id' => 'OGS-pandamania'
// 						),
// 						array(
// 							'game_name' => 'Pizza Prize',
// 							'english_name' => 'Pizza Prize',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-pizzaprize',
// 							'external_game_id' => 'OGS-pizzaprize'
// 						),
// 						array(
// 							'game_name' => 'Potion Commotion',
// 							'english_name' => 'Potion Commotion',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-potioncommotion',
// 							'external_game_id' => 'OGS-potioncommotion'
// 						),
// 						array(
// 							'game_name' => 'Psycho',
// 							'english_name' => 'Psycho',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-psycho',
// 							'external_game_id' => 'OGS-psycho'
// 						),
// 						array(
// 							'game_name' => 'Ramesses Riches',
// 							'english_name' => 'Ramesses Riches',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-ramessesriches',
// 							'external_game_id' => 'OGS-ramessesriches'
// 						),
// 						array(
// 							'game_name' => 'Ramesses Riches Mini',
// 							'english_name' => 'Ramesses Riches Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-ramessesrichesmini',
// 							'external_game_id' => 'OGS-ramessesrichesmini'
// 						),
// 						array(
// 							'game_name' => 'Shaaark! Superbet',
// 							'english_name' => 'Shaaark! Superbet',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-shaaarksuperbet',
// 							'external_game_id' => 'OGS-shaaarksuperbet'
// 						),
// 						array(
// 							'game_name' => 'Spanish Eyes',
// 							'english_name' => 'Spanish Eyes',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-spanisheyes',
// 							'external_game_id' => 'OGS-spanisheyes'
// 						),
// 						array(
// 							'game_name' => 'Starmania',
// 							'english_name' => 'Starmania',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-starmania',
// 							'external_game_id' => 'OGS-starmania'
// 						),
// 						array(
// 							'game_name' => 'Super Safari',
// 							'english_name' => 'Super Safari',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-supersafari',
// 							'external_game_id' => 'OGS-supersafari'
// 						),
// 						array(
// 							'game_name' => 'Spin Sorceress',
// 							'english_name' => 'Spin Sorceress',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-spinsorceress',
// 							'external_game_id' => 'OGS-spinsorceress'
// 						),
// 						array(
// 							'game_name' => 'Teddy Bears\' Picnic',
// 							'english_name' => 'Teddy Bears\' Picnic',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-teddybearspicnic',
// 							'external_game_id' => 'OGS-teddybearspicnic'
// 						),
// 						array(
// 							'game_name' => 'The Bermuda Mysteries',
// 							'english_name' => 'The Bermuda Mysteries',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-thebermudamysteries',
// 							'external_game_id' => 'OGS-thebermudamysteries'
// 						),
// 						array(
// 							'game_name' => 'The Codfather',
// 							'english_name' => 'The Codfather',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-thecodfather',
// 							'external_game_id' => 'OGS-thecodfather'
// 						),
// 						array(
// 							'game_name' => 'The Codfather',
// 							'english_name' => 'The Codfather',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-thecodfather',
// 							'external_game_id' => 'OGS-thecodfather'
// 						),
// 						array(
// 							'game_name' => 'The Snake Charmer',
// 							'english_name' => 'The Snake Charmer',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-thesnakecharmer',
// 							'external_game_id' => 'OGS-thesnakecharmer'
// 						),
// 						array(
// 							'game_name' => 'The Snake Charmer Mini',
// 							'english_name' => 'The Snake Charmer Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-thesnakecharmermini',
// 							'external_game_id' => 'OGS-thesnakecharmermini'
// 						),
// 						array(
// 							'game_name' => 'The Spin Lab',
// 							'english_name' => 'The Spin Lab',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-thespinlab',
// 							'external_game_id' => 'OGS-thespinlab'
// 						),
// 						array(
// 							'game_name' => 'Titan Storm',
// 							'english_name' => 'Titan Storm',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-titanstorm',
// 							'external_game_id' => 'OGS-titanstorm'
// 						),
// 						array(
// 							'game_name' => 'Tootin Car Man',
// 							'english_name' => 'Tootin Car Man',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-tootincarman',
// 							'external_game_id' => 'OGS-tootincarman'
// 						),
// 						array(
// 							'game_name' => 'Unicorn Legend',
// 							'english_name' => 'Unicorn Legend',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-unicornlegend',
// 							'external_game_id' => 'OGS-unicornlegend'
// 						),
// 						array(
// 							'game_name' => 'Venetian Rose',
// 							'english_name' => 'Venetian Rose',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-venetianrose',
// 							'external_game_id' => 'OGS-venetianrose'
// 						),
// 						array(
// 							'game_name' => 'Venetian Rose Mini',
// 							'english_name' => 'Venetian Rose Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-venetianrosemini',
// 							'external_game_id' => 'OGS-venetianrosemini'
// 						),
// 						array(
// 							'game_name' => 'Wild West',
// 							'english_name' => 'Wild West',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-wildwest',
// 							'external_game_id' => 'OGS-wildwest'
// 						),
// 						array(
// 							'game_name' => 'Wildcat Canyon',
// 							'english_name' => 'Wildcat Canyon',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-wildcatcanyon',
// 							'external_game_id' => 'OGS-wildcatcanyon'
// 						),
// 						array(
// 							'game_name' => 'Witch Pickings',
// 							'english_name' => 'Witch Pickings',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-witchpickings',
// 							'external_game_id' => 'OGS-witchpickings'
// 						),
// 						array(
// 							'game_name' => 'Volcano Eruption',
// 							'english_name' => 'Volcano Eruption',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-volcanoeruption',
// 							'external_game_id' => 'OGS-volcanoeruption'
// 						),
// 						array(
// 							'game_name' => 'Volcano Eruption Mini',
// 							'english_name' => 'Volcano Eruption Mini',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-volcanoeruptionmini',
// 							'external_game_id' => 'OGS-volcanoeruptionmini'
// 						),
// 						array(
// 							'game_name' => 'Dragon Born',
// 							'english_name' => 'Dragon Born',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-dragonborn',
// 							'external_game_id' => 'OGS-dragonborn'
// 						),
// 						array(
// 							'game_name' => 'GOLD',
// 							'english_name' => 'GOLD',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-gold',
// 							'external_game_id' => 'OGS-gold'
// 						),
// 						array(
// 							'game_name' => 'Aladdin\'s Legacy',
// 							'english_name' => 'Aladdin\'s Legacy',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-aladdinslegacy',
// 							'external_game_id' => 'OGS-aladdinslegacy'
// 						),
// 						array(
// 							'game_name' => 'Bars and Bells',
// 							'english_name' => 'Bars and Bells',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-barsandbells',
// 							'external_game_id' => 'OGS-barsandbells'
// 						),
// 						array(
// 							'game_name' => 'Dragon 8s',
// 							'english_name' => 'Dragon 8s',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-dragon8s',
// 							'external_game_id' => 'OGS-dragon8s'
// 						),
// 						array(
// 							'game_name' => 'Fortunes of the Amazon',
// 							'english_name' => 'Fortunes of the Amazon',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-fortunesoftheamazon',
// 							'external_game_id' => 'OGS-fortunesoftheamazon'
// 						),
// 						array(
// 							'game_name' => 'Gullivers Travels',
// 							'english_name' => 'Gullivers Travels',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-gulliverstravels',
// 							'external_game_id' => 'OGS-gulliverstravels'
// 						),
// 						array(
// 							'game_name' => 'Leonidas',
// 							'english_name' => 'Leonidas',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-leonidas',
// 							'external_game_id' => 'OGS-leonidas'
// 						),
// 						array(
// 							'game_name' => 'Shogun Showdown',
// 							'english_name' => 'Shogun Showdown',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-shogunshowdown',
// 							'external_game_id' => 'OGS-shogunshowdown'
// 						),
// 						array(
// 							'game_name' => 'Sinful Spins',
// 							'english_name' => 'Sinful Spins',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-sinfulspins',
// 							'external_game_id' => 'OGS-sinfulspins'
// 						),
// 						array(
// 							'game_name' => 'Thundering Zeus',
// 							'english_name' => 'Thundering Zeus',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-thunderingzeus',
// 							'external_game_id' => 'OGS-thunderingzeus'
// 						),
// 						array(
// 							'game_name' => 'Vampires vs. Werewolves',
// 							'english_name' => 'Vampires vs. Werewolves',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-vampiresvswerewolves',
// 							'external_game_id' => 'OGS-vampiresvswerewolves'
// 						),
// 						array(
// 							'game_name' => 'Angel\'s Touch',
// 							'english_name' => 'Angel\'s Touch',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-angelstouch',
// 							'external_game_id' => 'OGS-angelstouch'
// 						),
// 						array(
// 							'game_name' => 'Chilli Gold',
// 							'english_name' => 'Chilli Gold',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-chilligold',
// 							'external_game_id' => 'OGS-chilligold'
// 						),
// 						array(
// 							'game_name' => 'Diamond Tower',
// 							'english_name' => 'Diamond Tower',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-diamondtower',
// 							'external_game_id' => 'OGS-diamondtower'
// 						),
// 						array(
// 							'game_name' => 'Dolphin Gold',
// 							'english_name' => 'Dolphin Gold',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-dolphingold',
// 							'external_game_id' => 'OGS-dolphingold'
// 						),
// 						array(
// 							'game_name' => 'Druidess Gold',
// 							'english_name' => 'Druidess Gold',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-druidessgold',
// 							'external_game_id' => 'OGS-druidessgold'
// 						),
// 						array(
// 							'game_name' => 'Fortune 8 Cat',
// 							'english_name' => 'Fortune 8 Cat',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-fortune8cat',
// 							'external_game_id' => 'OGS-fortune8cat'
// 						),
// 						array(
// 							'game_name' => 'Frogs \'n Flies',
// 							'english_name' => 'Frogs \'n Flies',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-frogsnflies',
// 							'external_game_id' => 'OGS-frogsnflies'
// 						),
// 						array(
// 							'game_name' => 'Lost Temple',
// 							'english_name' => 'Lost Temple',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-losttemple',
// 							'external_game_id' => 'OGS-losttemple'
// 						),
// 						array(
// 							'game_name' => 'Moon Temple',
// 							'english_name' => 'Moon Temple',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-moontemple',
// 							'external_game_id' => 'OGS-moontemple'
// 						),
// 						array(
// 							'game_name' => 'More Monkeys',
// 							'english_name' => 'More Monkeys',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-moremonkeys',
// 							'external_game_id' => 'OGS-moremonkeys'
// 						),
// 						array(
// 							'game_name' => 'Pixie Gold',
// 							'english_name' => 'Pixie Gold',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-pixiegold',
// 							'external_game_id' => 'OGS-pixiegold'
// 						),
// 						array(
// 							'game_name' => 'Samurai Princess',
// 							'english_name' => 'Samurai Princess',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-samuraiprincess',
// 							'external_game_id' => 'OGS-samuraiprincess'
// 						),
// 						array(
// 							'game_name' => 'Serengeti Diamonds',
// 							'english_name' => 'Serengeti Diamonds',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-serengetidiamonds',
// 							'external_game_id' => 'OGS-serengetidiamonds'
// 						),
// 						array(
// 							'game_name' => '1429 Uncharted Seas',
// 							'english_name' => '1429 Uncharted Seas',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-1429unchartedseas',
// 							'external_game_id' => 'OGS-1429unchartedseas'
// 						),
// 						array(
// 							'game_name' => 'Arcader',
// 							'english_name' => 'Arcader',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-arcader',
// 							'external_game_id' => 'OGS-arcader'
// 						),
// 						array(
// 							'game_name' => 'Barber Shop',
// 							'english_name' => 'Barber Shop',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-barbershop',
// 							'external_game_id' => 'OGS-barbershop'
// 						),
// 						array(
// 							'game_name' => 'Birds On A Wire',
// 							'english_name' => 'Birds On A Wire',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-birdsonawire',
// 							'external_game_id' => 'OGS-birdsonawire'
// 						),
// 						array(
// 							'game_name' => 'Bork The Berzerker',
// 							'english_name' => 'Bork The Berzerker',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-borktheberzerker',
// 							'external_game_id' => 'OGS-borktheberzerker'
// 						),
// 						array(
// 							'game_name' => 'Esqueleto Explosivo',
// 							'english_name' => 'Esqueleto Explosivo',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-esqueletoexplosivo',
// 							'external_game_id' => 'OGS-esqueletoexplosivo'
// 						),
// 						array(
// 							'game_name' => 'Flux',
// 							'english_name' => 'Flux',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-flux',
// 							'external_game_id' => 'OGS-flux'
// 						),
// 						array(
// 							'game_name' => 'Fruit Warp',
// 							'english_name' => 'Fruit Warp',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-fruitwarp',
// 							'external_game_id' => 'OGS-fruitwarp'
// 						),
// 						array(
// 							'game_name' => 'Magicious',
// 							'english_name' => 'Magicious',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-magicious',
// 							'external_game_id' => 'OGS-magicious'
// 						),
// 						array(
// 							'game_name' => 'Sunny Scoops',
// 							'english_name' => 'Sunny Scoops',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>0,
// 							'game_code' => 'OGS-sunnyscoops',
// 							'external_game_id' => 'OGS-sunnyscoops'
// 						),
// 						array(
// 							'game_name' => 'Toki Time',
// 							'english_name' => 'Toki Time',
// 							'flash_enabled' =>1,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-tokitime',
// 							'external_game_id' => 'OGS-tokitime'
// 						)
// 					),
// 				),
// 				array(
// 					'game_type' => 'Table Game',
// 					'game_type_lang' => 'qt_table_games',
// 					'status' => self::FLAG_TRUE,
// 					'flag_show_in_site' => self::FLAG_TRUE,
// 					'game_description_list' => array(
// 						array(
// 							'game_name' => 'BlackjackPro MonteCarlo Multihand',
// 							'english_name' => 'BlackjackPro MonteCarlo Multihand',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-blackjackpromontecarlomultihand',
// 							'external_game_id' => 'OGS-blackjackpromontecarlomultihand'
// 						),
// 						array(
// 							'game_name' => 'BlackjackPro MonteCarlo Singlehand',
// 							'english_name' => 'BlackjackPro MonteCarlo Singlehand',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-blackjackpromontecarlosinglehand',
// 							'external_game_id' => 'OGS-blackjackpromontecarlosinglehand'
// 						),
// 						array(
// 							'game_name' => 'Roulette Master',
// 							'english_name' => 'Roulette Master',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-roulettemaster',
// 							'external_game_id' => 'OGS-roulettemaster'
// 						),
// 						array(
// 							'game_name' => 'Blackjack',
// 							'english_name' => 'Blackjack',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-blackjack',
// 							'external_game_id' => 'OGS-blackjack'
// 						),
// 						array(
// 							'game_name' => 'European Blackjack',
// 							'english_name' => 'European Blackjack',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-europeanblackjack',
// 							'external_game_id' => 'OGS-europeanblackjack'
// 						),
// 						array(
// 							'game_name' => 'European Roulette',
// 							'english_name' => 'European Roulette',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-europeanroulette',
// 							'external_game_id' => 'OGS-europeanroulette'
// 						),
// 						array(
// 							'game_name' => 'No Commission Baccarat',
// 							'english_name' => 'No Commission Baccarat',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-nocommissionbaccarat',
// 							'external_game_id' => 'OGS-nocommissionbaccarat'
// 						),
// 						array(
// 							'game_name' => 'Roulette (American)',
// 							'english_name' => 'Roulette (American)',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-rouletteamerican',
// 							'external_game_id' => 'OGS-rouletteamerican'
// 						),
// 						array(
// 							'game_name' => 'SideBet Blackjack',
// 							'english_name' => 'SideBet Blackjack',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-sidebetblackjack',
// 							'external_game_id' => 'OGS-sidebetblackjack'
// 						)
// 					),
// 				),
// 				array(
// 					'game_type' => 'Video Poker',
// 					'game_type_lang' => 'qt_video_poker',
// 					'status' => self::FLAG_TRUE,
// 					'flag_show_in_site' => self::FLAG_TRUE,
// 					'game_description_list' => array(
// 						array(
// 							'game_name' => 'All American',
// 							'english_name' => 'All American',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-allamerican',
// 							'external_game_id' => 'OGS-allamerican'
// 						),
// 						array(
// 							'game_name' => 'Deuces Wild',
// 							'english_name' => 'Deuces Wild',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-deuceswild',
// 							'external_game_id' => 'OGS-deuceswild'
// 						),
// 						array(
// 							'game_name' => 'Jacks or Better',
// 							'english_name' => 'Jacks or Better',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-jacksorbetter',
// 							'external_game_id' => 'OGS-jacksorbetter'
// 						),
// 						array(
// 							'game_name' => 'Joker Poker',
// 							'english_name' => 'Joker Poker',
// 							'html_five_enabled' =>0,
// 							'flash_enabled' =>1,
// 							'game_code' => 'OGS-jokerpoker',
// 							'external_game_id' => 'OGS-jokerpoker'
// 						)
// 					),
// 				),
// 				array(
// 					'game_type' => 'Scratchcard',
// 					'game_type_lang' => 'qt_scratchcard',
// 					'status' => self::FLAG_TRUE,
// 					'flag_show_in_site' => self::FLAG_TRUE,
// 					'game_description_list' => array(
// 						array(
// 							'game_name' => 'Scratch Call of the Colosseum',
// 							'english_name' => 'Scratch Call of the Colosseum',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scracthcallofthecolosseum',
// 							'external_game_id' => 'OGS-scracthcallofthecolosseum'
// 						),
// 						array(
// 							'game_name' => 'Scratch Dr Love',
// 							'english_name' => 'Scratch Dr Love',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchdrlove',
// 							'external_game_id' => 'OGS-scratchdrlove'
// 						),
// 						array(
// 							'game_name' => 'Scratch Dr Love On Vacation',
// 							'english_name' => 'Scratch Dr Love On Vacation',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchdrloveonvacatione',
// 							'external_game_id' => 'OGS-scratchdrloveonvacatione'
// 						),
// 						array(
// 							'game_name' => 'Scratch Emperors Garden',
// 							'english_name' => 'Scratch Emperors Garden',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchemperorsgarden',
// 							'external_game_id' => 'OGS-scratchemperorsgarden'
// 						),
// 						array(
// 							'game_name' => 'Scratch Foxin Wins',
// 							'english_name' => 'Scratch Foxin Wins',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchfoxinwins',
// 							'external_game_id' => 'OGS-scratchfoxinwins'
// 						),
// 						array(
// 							'game_name' => 'Scratch Gorilla Go Wild',
// 							'english_name' => 'Scratch Gorilla Go Wild',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchgorillagowild',
// 							'external_game_id' => 'OGS-scratchgorillagowild'
// 						),
// 						array(
// 							'game_name' => 'Scratch Irish Eyes',
// 							'english_name' => 'Scratch Irish Eyes',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchirisheyes',
// 							'external_game_id' => 'OGS-scratchirisheyes'
// 						),
// 						array(
// 							'game_name' => 'Scratch Irish Eyes 2',
// 							'english_name' => 'Scratch Irish Eyes 2',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchirisheyes2',
// 							'external_game_id' => 'OGS-scratchirisheyes2'
// 						),
// 						array(
// 							'game_name' => 'Scratch Medusa',
// 							'english_name' => 'Scratch Medusa',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchmedusa',
// 							'external_game_id' => 'OGS-scratchmedusa'
// 						),
// 						array(
// 							'game_name' => 'Scratch Merlins Millions',
// 							'english_name' => 'Scratch Merlins Millions',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchmerlinsmillions',
// 							'external_game_id' => 'OGS-scratchmerlinsmillions'
// 						),
// 						array(
// 							'game_name' => 'Scratch Ramesses Riches',
// 							'english_name' => 'Scratch Ramesses Riches',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchramessesriches',
// 							'external_game_id' => 'OGS-scratchramessesriches'
// 						),
// 						array(
// 							'game_name' => 'Scratch The Codfather',
// 							'english_name' => 'Scratch The Codfather',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchthecodfather',
// 							'external_game_id' => 'OGS-scratchthecodfather'
// 						),
// 						array(
// 							'game_name' => 'Scratch Volcano Eruption',
// 							'english_name' => 'Scratch Volcano Eruption',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchvolcanoeruption',
// 							'external_game_id' => 'OGS-scratchvolcanoeruption'
// 						),
// 						array(
// 							'game_name' => 'Scratch Pandamania',
// 							'english_name' => 'Scratch Pandamania',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchpandamania',
// 							'external_game_id' => 'OGS-scratchpandamania'
// 						),
// 						array(
// 							'game_name' => 'Scratch Genie Wild',
// 							'english_name' => 'Scratch Genie Wild',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchgeniewild',
// 							'external_game_id' => 'OGS-scratchgeniewild'
// 						),
// 						array(
// 							'game_name' => 'Scratch Oil Mania',
// 							'english_name' => 'Scratch Oil Mania',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchoilmania',
// 							'external_game_id' => 'OGS-scratchoilmania'
// 						),
// 						array(
// 							'game_name' => 'Scratch Big Foot',
// 							'english_name' => 'Scratch Big Foot',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchbigfoot',
// 							'external_game_id' => 'OGS-scratchbigfoot'
// 						),
// 						array(
// 							'game_name' => 'Scratch The Snake Charmer',
// 							'english_name' => 'Scratch The Snake Charmer',
// 							'flash_enabled' =>0,
// 							'html_five_enabled' =>1,
// 							'game_code' => 'OGS-scratchthesnakecharmer',
// 							'external_game_id' => 'OGS-scratchthesnakecharmer'
// 						)
// 					),
// 				)
// 		);

// $game_description_list = array();
// foreach ($data as $game_type) {

// 	$this->db->insert('game_type', array(
// 		'game_platform_id' => QT_API,
// 		'game_type' => $game_type['game_type'],
// 		'game_type_lang' => $game_type['game_type_lang'],
// 		'status' => $game_type['status'],
// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
// 		));

// 	$game_type_id = $this->db->insert_id();
// 	foreach ($game_type['game_description_list'] as $game_description) {
// 		$game_description_list[] = array_merge(array(
// 			'game_platform_id' => QT_API,
// 			'game_type_id' => $game_type_id,
// 			), $game_description);
// 	}

// }

// $this->db->insert_batch('game_description', $game_description_list);
// $this->db->trans_complete();

}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => QT_API));
	// $this->db->delete('game_description', array('game_platform_id' => QT_API));
	// $this->db->trans_complete();
}
}