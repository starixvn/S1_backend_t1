<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_AGHG_to_game_type_and_game_description_201609300926 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

// 		$this->db->trans_start();

// 		$data = array(


// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '百家樂1',
// 						'english_name' => 'Casino Baccarat 1',
// 						'external_game_id' => 'l8i2hq4jo2hjj9ca',
// 						'game_code' => 'l8i2hq4jo2hjj9ca'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '百家樂2',
// 						'english_name' => 'Casino Baccarat 2',
// 						'external_game_id' => 'bacdhq2j04hjj8ca',
// 						'game_code' => 'bacdhq2j04hjj8ca'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '百家樂3',
// 						'english_name' => 'Casino Baccarat 3',
// 						'external_game_id' => 'cfnfubozrmdpyj3h',
// 						'game_code' => 'cfnfubozrmdpyj3h'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '尊貴百家樂4',
// 						'english_name' => 'Casino Baccarat 4',
// 						'external_game_id' => 'bc4drq1k02hnj8cd',
// 						'game_code' => 'bc4drq1k02hnj8cd'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '尊貴百家樂5',
// 						'english_name' => 'Casino Baccarat 5',
// 						'external_game_id' => 'bc5drt1kr2bsj4ch',
// 						'game_code' => 'bc5drt1kr2bsj4ch'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Baccarat',
// 				'game_type_lang' => 'baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '百家樂6',
// 						'english_name' => 'Casino Baccarat 6',
// 						'external_game_id' => 'bc6grt7ka9hmjkcz',
// 						'game_code' => 'bc6grt7ka9hmjkcz'
// 						)
// 					),
// 				),
// 			array('game_type' => 'NCBaccarat',
// 				'game_type_lang' => 'ncbaccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '免水百家樂',
// 						'english_name' => 'Casino NCBaccarat',
// 						'external_game_id' => 'ncbaccj5jr2kplmj',
// 						'game_code' => 'ncbaccj5jr2kplmj'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Sic Bo',
// 				'game_type_lang' => 'sic_bo',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '骰寶',
// 						'english_name' => 'Casino SicBo',
// 						'external_game_id' => 'wnp11c348ew618ci',
// 						'game_code' => 'wnp11c348ew618ci'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Roulette',
// 				'game_type_lang' => 'roulette',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '輪盤1',
// 						'english_name' => 'Casino Roulette',
// 						'external_game_id' => 'x9i3jxq3kiyxx670',
// 						'game_code' => 'x9i3jxq3kiyxx670'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Roulette',
// 				'game_type_lang' => 'roulette',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '輪盤2',
// 						'english_name' => 'Casino Roulette 2',
// 						'external_game_id' => 'x6zwdu87zqe43ksd',
// 						'game_code' => 'x6zwdu87zqe43ksd'
// 						)
// 					),
// 				),
// 			array('game_type' => 'DragonTiger',
// 				'game_type_lang' => 'dragontiger',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '龍虎',
// 						'english_name' => 'Casino Dragon Tiger',
// 						'external_game_id' => 'dat2hq6jo4hjh8dt',
// 						'game_code' => 'dat2hq6jo4hjh8dt'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Blackjack',
// 				'game_type_lang' => 'blackjack',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '二十一點1',
// 						'english_name' => 'Casino Black Jack 1',
// 						'external_game_id' => 'bj2m46lf2wguzbya',
// 						'game_code' => 'bj2m46lf2wguzbya'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Blackjack',
// 				'game_type_lang' => 'blackjack',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '二十一點2',
// 						'english_name' => 'Casino Black Jack 2',
// 						'external_game_id' => 'yfjv7uzd11rq4ecy',
// 						'game_code' => 'yfjv7uzd11rq4ecy'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Blackjack',
// 				'game_type_lang' => 'blackjack',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '二十一點3',
// 						'english_name' => 'Casino VIP BlackJack',
// 						'external_game_id' => 'bj3m46lf2wguzby3',
// 						'game_code' => 'bj3m46lf2wguzby3'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Slots',
// 				'game_type_lang' => 'rng_slots',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '火焰777',
// 						'english_name' => 'Rng Slots',
// 						'external_game_id' => '731c0ic7s8el835d',
// 						'game_code' => '731c0ic7s8el835d'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Slots Chance',
// 				'game_type_lang' => 'rng_slots_chance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '火焰777',
// 						'english_name' => 'Rng Slots Chance',
// 						'external_game_id' => '7k78h0c9eq1wklby',
// 						'game_code' => '7k78h0c9eq1wklby'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Baccarat',
// 				'game_type_lang' => 'rng_baccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '百家樂',
// 						'english_name' => 'Rng Baccarat',
// 						'external_game_id' => '24r2hwo7m6zaknkp',
// 						'game_code' => '24r2hwo7m6zaknkp'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Blackjack',
// 				'game_type_lang' => 'rng_blackjack',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '二十一點',
// 						'english_name' => 'Rng Blackjack',
// 						'external_game_id' => '0suxq7m815j9vaer',
// 						'game_code' => '0suxq7m815j9vaer'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Carebbean Poker',
// 				'game_type_lang' => 'rng_carebbean_poker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '加勒比海撲克',
// 						'english_name' => 'Rng Carebbean Poker',
// 						'external_game_id' => '6wjz46hvko913lm4',
// 						'game_code' => '6wjz46hvko913lm4'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng CashBag',
// 				'game_type_lang' => 'rng_cashbag',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '現金滿袋',
// 						'english_name' => 'Rng CashBag',
// 						'external_game_id' => 'hnfd76shwsozrnwj',
// 						'game_code' => 'hnfd76shwsozrnwj'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng CashParty',
// 				'game_type_lang' => 'rng_cashparty',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '閃亮派對',
// 						'english_name' => 'Rng CashParty',
// 						'external_game_id' => 't4fd7tsh5so6rnw8',
// 						'game_code' => 't4fd7tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Casino War',
// 				'game_type_lang' => 'rng_casino_war',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '娛樂場之戰',
// 						'english_name' => 'Rng Casino War',
// 						'external_game_id' => 'ng7cukrwub7xzstj',
// 						'game_code' => 'ng7cukrwub7xzstj'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng CherryMania',
// 				'game_type_lang' => 'rng_cherrymania',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '櫻桃工坊',
// 						'english_name' => 'Rng CherryMania',
// 						'external_game_id' => 'dphf72iwxhozrnwf',
// 						'game_code' => 'dphf72iwxhozrnwf'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng ClassicJackSorBetter',
// 				'game_type_lang' => 'rng_classicjacksorbetter',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '經典傑克高手',
// 						'english_name' => 'Rng ClassicJackSorBetter',
// 						'external_game_id' => 'k5fd55shwsozrnwo',
// 						'game_code' => 'k5fd55shwsozrnwo'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng ClassicJokerPoker',
// 				'game_type_lang' => 'rng_classicjokerpoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '經典小丑撲克',
// 						'english_name' => 'Rng ClassicJokerPoker',
// 						'external_game_id' => 's4fd34shwsozrnwn',
// 						'game_code' => 's4fd34shwsozrnwn'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng ClassicVideoPoker',
// 				'game_type_lang' => 'rng_classicvideopoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '經典視頻撲克',
// 						'english_name' => 'Rng ClassicVideoPoker',
// 						'external_game_id' => 'h2fd22shwsozrnww',
// 						'game_code' => 'h2fd22shwsozrnww'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng JacksorBetter',
// 				'game_type_lang' => 'rng_jacksorbetter',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '傑克高手',
// 						'english_name' => 'Rng JacksorBetter',
// 						'external_game_id' => 'jypd73swxhozrnwx',
// 						'game_code' => 'jypd73swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng JacksorBetterChance',
// 				'game_type_lang' => 'rng_jacksorbetterchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '傑克高手',
// 						'english_name' => 'Rng JacksorBetterChance',
// 						'external_game_id' => 'ktpd74swxhozrnwx',
// 						'game_code' => 'ktpd74swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Jokerpoker',
// 				'game_type_lang' => 'rng_jokerpoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '小丑撲克',
// 						'english_name' => 'Rng Jokerpoker',
// 						'external_game_id' => 'bl3bna7a1z18tyir',
// 						'game_code' => 'bl3bna7a1z18tyir'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng JokerpokerChance',
// 				'game_type_lang' => 'rng_jokerpokerchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '小丑撲克',
// 						'english_name' => 'Rng JokerpokerChance',
// 						'external_game_id' => 'pqjhxbnro0prbcv1',
// 						'game_code' => 'pqjhxbnro0prbcv1'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Videopoker',
// 				'game_type_lang' => 'rng_videopoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '視頻撲克',
// 						'english_name' => 'Rng Videopoker',
// 						'external_game_id' => 'wks6njmjoop6pvnu',
// 						'game_code' => 'wks6njmjoop6pvnu'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng VideopokerChance',
// 				'game_type_lang' => 'rng_videopokerchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '視頻撲克',
// 						'english_name' => 'Rng VideopokerChance',
// 						'external_game_id' => 'uwpd76swxhozrnwx',
// 						'game_code' => 'uwpd76swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng GemFortune',
// 				'game_type_lang' => 'rng_gemfortune',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '幸運寶石',
// 						'english_name' => 'Rng GemFortune',
// 						'external_game_id' => 'p2jd73uwxhozrnwh',
// 						'game_code' => 'p2jd73uwxhozrnwh'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng GoldenGopher',
// 				'game_type_lang' => 'rng_goldengopher',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '黃金地鼠',
// 						'english_name' => 'Rng GoldenGopher',
// 						'external_game_id' => 'i6ed15twxhozrnwt',
// 						'game_code' => 'i6ed15twxhozrnwt'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng GoldenSevens',
// 				'game_type_lang' => 'rng_goldensevens',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '金裝三重7',
// 						'english_name' => 'Rng GoldenSevens',
// 						'external_game_id' => 'tkpd74ywxhozrnwr',
// 						'game_code' => 'tkpd74ywxhozrnwr'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng DoubleUpPoker',
// 				'game_type_lang' => 'rng_doubleuppoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '日式翻倍撲克',
// 						'english_name' => 'Rng DoubleUpPoker',
// 						'external_game_id' => 'j5ed26rwxhozrnws',
// 						'game_code' => 'j5ed26rwxhozrnws'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng DoubleUpPokerChance',
// 				'game_type_lang' => 'rng_doubleuppokerchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '日式翻倍撲克',
// 						'english_name' => 'Rng DoubleUpPokerChance',
// 						'external_game_id' => 'k4fd56shwsozrnwj',
// 						'game_code' => 'k4fd56shwsozrnwj'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Letitride Poker',
// 				'game_type_lang' => 'rng_letitride_poker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '任逍遙撲克',
// 						'english_name' => 'Rng Letitride Poker',
// 						'external_game_id' => 'ivybhtf7ib2p5uy6',
// 						'game_code' => 'ivybhtf7ib2p5uy6'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Lucky888',
// 				'game_type_lang' => 'rng_lucky888',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '幸運888',
// 						'english_name' => 'Rng Lucky888',
// 						'external_game_id' => 'xwqd89swxzohrnwa',
// 						'game_code' => 'xwqd89swxzohrnwa'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng LuckyHarvest',
// 				'game_type_lang' => 'rng_luckyharvest',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '幸運大豐收',
// 						'english_name' => 'Rng LuckyHarvest',
// 						'external_game_id' => 'oked75twxhozrnwr',
// 						'game_code' => 'oked75twxhozrnwr'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng LuckyHarvestChance',
// 				'game_type_lang' => 'rng_luckyharvestchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '幸運大豐收',
// 						'english_name' => 'Rng LuckyHarvestChance',
// 						'external_game_id' => 'rfed76rwxhozrnwt',
// 						'game_code' => 'rfed76rwxhozrnwt'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng MangoMania',
// 				'game_type_lang' => 'rng_mangomania',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '芒果工坊',
// 						'english_name' => 'Rng MangoMania',
// 						'external_game_id' => 'g3fd6tsh5so6rnw8',
// 						'game_code' => 'g3fd6tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Roulette',
// 				'game_type_lang' => 'rng_roulette',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '輪盤',
// 						'english_name' => 'Rng Roulette',
// 						'external_game_id' => '167s5h696uml7rq6',
// 						'game_code' => '167s5h696uml7rq6'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng BigSam',
// 				'game_type_lang' => 'rng_bigsam',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '大山姆',
// 						'english_name' => 'Rng BigSam',
// 						'external_game_id' => 'fwud77swxhozrnwx',
// 						'game_code' => 'fwud77swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng MagicForest',
// 				'game_type_lang' => 'rng_magicforest',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '魔法森林',
// 						'english_name' => 'Rng MagicForest',
// 						'external_game_id' => 'hhpd72swxhozrnwx',
// 						'game_code' => 'hhpd72swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Snapjax',
// 				'game_type_lang' => 'rng_snapjax',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '莊家二十一點',
// 						'english_name' => 'Rng Snapjax',
// 						'external_game_id' => 'i7fd2tsh5so6rnw8',
// 						'game_code' => 'i7fd2tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Keno',
// 				'game_type_lang' => 'rng_keno',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '基諾',
// 						'english_name' => 'Rng Keno',
// 						'external_game_id' => 'jled1a5t1d5qivqv',
// 						'game_code' => 'jled1a5t1d5qivqv'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng JacksorBetterMulti',
// 				'game_type_lang' => 'rng_jacksorbettermulti',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '傑克高手',
// 						'english_name' => 'Rng JacksorBetterMulti',
// 						'external_game_id' => 'lrpd75swxhozrnwx',
// 						'game_code' => 'lrpd75swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng JacksorBetterMultiChance',
// 				'game_type_lang' => 'rng_jacksorbettermultichance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '傑克高手',
// 						'english_name' => 'Rng JacksorBetterMultiChance',
// 						'external_game_id' => 'nepd76swxhozrnwx',
// 						'game_code' => 'nepd76swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng DragonTiger',
// 				'game_type_lang' => 'rng_dragontiger',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '龍虎',
// 						'english_name' => 'Rng DragonTiger',
// 						'external_game_id' => 'r4fd1tsh6so7rnw9',
// 						'game_code' => 'r4fd1tsh6so7rnw9'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng FrenchRoulette',
// 				'game_type_lang' => 'rng_frenchroulette',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '法式輪盤',
// 						'english_name' => 'Rng FrenchRoulette',
// 						'external_game_id' => 'u6fd9tsh5so6rnw8',
// 						'game_code' => 'u6fd9tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng SqzBaccarat',
// 				'game_type_lang' => 'rng_sqzbaccarat',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '瞇牌百家樂',
// 						'english_name' => 'Rng SqzBaccarat',
// 						'external_game_id' => 'w8fd1tsh5so6rnw8',
// 						'game_code' => 'w8fd1tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng SkillSlot',
// 				'game_type_lang' => 'rng_skillslot',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '技巧老虎機',
// 						'english_name' => 'Rng SkillSlot',
// 						'external_game_id' => 'wddd78swxhozrnwx',
// 						'game_code' => 'wddd78swxhozrnwx'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Oasis Poker',
// 				'game_type_lang' => 'rng_oasis_poker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '綠洲撲克',
// 						'english_name' => 'Rng Oasis Poker',
// 						'external_game_id' => 'kt4kqo9fo49qxk6l',
// 						'game_code' => 'kt4kqo9fo49qxk6l'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Fruit Slots',
// 				'game_type_lang' => 'rng_fruit_slots',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '果園獎金',
// 						'english_name' => 'Rng Fruit Slots',
// 						'external_game_id' => 'dpcaki3dl49c8h3y',
// 						'game_code' => 'dpcaki3dl49c8h3y'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Paigowpoker',
// 				'game_type_lang' => 'rng_paigowpoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '牌九撲克',
// 						'english_name' => 'Rng Paigowpoker',
// 						'external_game_id' => 'vici41ubxt8cfuny',
// 						'game_code' => 'vici41ubxt8cfuny'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng PinkDiamond',
// 				'game_type_lang' => 'rng_pinkdiamond',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '粉紅鑽石',
// 						'english_name' => 'Rng PinkDiamond',
// 						'external_game_id' => 'wefr78shxwozrnwe',
// 						'game_code' => 'wefr78shxwozrnwe'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng PinkDiamondChance',
// 				'game_type_lang' => 'rng_pinkdiamondchance',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '粉紅鑽石',
// 						'english_name' => 'Rng PinkDiamondChance',
// 						'external_game_id' => 'owfd77xwshozrnwd',
// 						'game_code' => 'owfd77xwshozrnwd'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng Reddog',
// 				'game_type_lang' => 'rng_reddog',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '紅狗撲克',
// 						'english_name' => 'Rng Reddog',
// 						'external_game_id' => 'kq120xdsctk9jrrt',
// 						'game_code' => 'kq120xdsctk9jrrt'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng RoadToRiches',
// 				'game_type_lang' => 'rng_roadtoriches',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '致富之路',
// 						'english_name' => 'Rng RoadToRiches',
// 						'external_game_id' => 'y5fd8tsh5so6rnw8',
// 						'game_code' => 'y5fd8tsh5so6rnw8'
// 						)
// 					),
// 				),
// 			array('game_type' => 'Rng ThreeCardPoker',
// 				'game_type_lang' => 'rng_threecardpoker',
// 				'status' => self::FLAG_TRUE,
// 				'flag_show_in_site' => self::FLAG_TRUE,
// 				'game_description_list' => array(
// 					array('game_name' => '三卡撲克',
// 						'english_name' => 'Rng ThreeCardPoker',
// 						'external_game_id' => 'qwxd78swxhozrnwx',
// 						'game_code' => 'qwxd78swxhozrnwx'
// 						)
// 					),
// 				),


// 		);//data

// $game_description_list = array();
// foreach ($data as $game_type) {

// 	$this->db->insert('game_type', array(
// 		'game_platform_id' => AGHG_API,
// 		'game_type' => $game_type['game_type'],
// 		'game_type_lang' => $game_type['game_type_lang'],
// 		'status' => $game_type['status'],
// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
// 		));

// 	$game_type_id = $this->db->insert_id();
// 	foreach ($game_type['game_description_list'] as $game_description) {
// 		$game_description_list[] = array_merge(array(
// 			'game_platform_id' => AGHG_API,
// 			'game_type_id' => $game_type_id,
// 			), $game_description);
// 	}

// }

// $this->db->insert_batch('game_description', $game_description_list);
// $this->db->trans_complete();

}

public function down() {
	// $this->db->trans_start();
	// $this->db->delete('game_type', array('game_platform_id' => AGHG_API, 'game_type !='=> 'unknown'));
	// $this->db->delete('game_description', array('game_platform_id' => AGHG_API,'game_name !='=> 'aghg.unknown'));
	// $this->db->trans_complete();
}
}