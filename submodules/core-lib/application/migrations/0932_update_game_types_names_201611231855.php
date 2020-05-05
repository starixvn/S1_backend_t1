<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_types_names_201611231855 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$game_types_pt = array(
			// 'Fixed Odds' => array(
			// 	'game_type' => '_json:{"1":"Fixed Odds","2":"手机游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Fixed Odds","2":"手机游戏"}',
			// ),
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Game","2":"百家乐"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Live Games' => array(
				'game_type' => '_json:{"1":"Live Games","2":"赌场游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"赌场游戏"}',
			),
			'Slot Machines' => array(
				'game_type' => '_json:{"1":"Slot Machines","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Slot Machines","2":"真人游戏"}',
			),
			'Scratchcards' => array(
				'game_type' => '_json:{"1":"Scratchcards","2":"彩票游戏"}',
				'game_type_lang' => '_json:{"1":"Scratchcards","2":"彩票游戏"}',
			),
			'Video Pokers' => array(
				'game_type' => '_json:{"1":"Video Pokers","2":"彩票游戏"}',
				'game_type_lang' => '_json:{"1":"Video Pokers","2":"彩票游戏"}',
			),
			// 'Sidegames' => array(
			// 	'game_type' => '_json:{"1":"Sidegames","2":"彩票游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Sidegames","2":"彩票游戏"}',
			// ),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_nt = array(
			'经典' => array(
				'game_type' => '_json:{"1":"Classical","2":"经典"}',
				'game_type_lang' => '_json:{"1":"Classical","2":"经典"}',
			),
			'电动老虎机' => array(
				'game_type' => '_json:{"1":"Video Slots","2":"电动老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slots","2":"电动老虎机"}',
			),
			'累积' => array(
				'game_type' => '_json:{"1":"Accumulation","2":"累积"}',
				'game_type_lang' => '_json:{"1":"Accumulation","2":"累积"}',
			),
			'迷你' => array(
				'game_type' => '_json:{"1":"Mini","2":"迷你"}',
				'game_type_lang' => '_json:{"1":"Mini","2":"迷你"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_mg = array(
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
			'Slots' => array(
				'game_type' => '_json:{"1":"Slots","2":" 老虎机"}',
				'game_type_lang' => '_json:{"1":"Slots","2":" 老虎机"}',
			),
			'Video Pokers' => array(
				'game_type' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
			),
			// 'Progressives' => array(
			// 	'game_type' => '_json:{"1":"Progressives","2":"不明"}',
			// 	'game_type_lang' => '_json:{"1":"Progressives","2":"不明"}',
			// ),
			'Scratchcards' => array(
				'game_type' => '_json:{"1":"Scratch Card Game","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratch Card Game","2":"刮刮乐游戏"}',
			),
			'Others' => array(
				'game_type' => '_json:{"1":"Others","2":"其他"}',
				'game_type_lang' => '_json:{"1":"Others","2":"其他"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_inteplay = array(
			'Video Slots' => array(
				'game_type' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
			),
			'Classic Slots' => array(
				'game_type' => '_json:{"1":"Classic Slots","2":"经典老虎机"}',
				'game_type_lang' => '_json:{"1":"Classic Slots","2":"经典老虎机"}',
			),
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Card Games' => array(
				'game_type' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
			),
			'Others' => array(
				'game_type' => '_json:{"1":"Others","2":"其他"}',
				'game_type_lang' => '_json:{"1":"Others","2":"其他"}',
			),
			// 'Third Games' => array(
			// 	'game_type' => '_json:{"1":"Third Games","2":"累计奖池"}',
			// 	'game_type_lang' => '_json:{"1":"Third Games","2":"累计奖池"}',
			// ),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_onesgame= array(
			'Slots' => array(
				'game_type' => '_json:{"1":"Slots","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"不明"}',
			),
			'Table' => array(
				'game_type' => '_json:{"1":"Table","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Table","2":"不明"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_gspt= array(
			'Card Games' => array(
				'game_type' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
			),
			// 'Fixed-Odds Games' => array(
			// 	'game_type' => '_json:{"1":"Fixed-Odds Games","2":"老虎机"}',
			// 	'game_type_lang' => '_json:{"1":"Fixed-Odds Games","2":"老虎机"}',
			// ),
			'Live Dealer Games' => array(
				'game_type' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
			),
			'Rng BigSam' => array(
				'game_type' => '_json:{"1":"Rng BigSam Games","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratch Cards Games","2":"刮刮乐游戏"}',
			),
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
			'Video Pokers' => array(
				'game_type' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
			),
			'Video Slots' => array(
				'game_type' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
			),
			'Live Games' => array(
				'game_type' => '_json:{"1":"Live Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏"}',
			),
			'Arcade' => array(
				'game_type' => '_json:{"1":"Arcade","2":"街机游戏"}',
				'game_type_lang' => '_json:{"1":"Arcade","2":"街机游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_gsag = array(
			'EBR' => array(
				'game_type' => '_json:{"1":"EGame","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"EGame","2":"视频扑克"}',
			),
			'BR' => array(
				'game_type' => '_json:{"1":"Live Games","2":"R老虎机"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"R老虎机"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_ab = array(
			'Live Games' => array(
				'game_type' => '_json:{"1":"Live Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"不明"}',
			),
		);

		$game_types_bs = array(
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"真人游戏"}',
			),
			'Multihand Poker' => array(
				'game_type' => '_json:{"1":"Multihand Poker","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Multihand Poker","2":"真人游戏"}',
			),
			'Soft Games' => array(
				'game_type' => '_json:{"1":"Soft Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Soft Games","2":"真人游戏"}',
			),
			'Slots' => array(
				'game_type' => '_json:{"1":"Slots","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"真人游戏"}',
			),
			'Pyramid Poker' => array(
				'game_type' => '_json:{"1":"Pyramid Poker","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Pyramid Poker","2":"真人游戏"}',
			),
			'Table' => array(
				'game_type' => '_json:{"1":"Table","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Table","2":"真人游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"不明"}',
			),
		);

		$game_types_ttg = array(
			'Card games' => array(
				'game_type' => '_json:{"1":"Card games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Card games","2":"真人游戏"}',
			),
			'Slots' => array(
				'game_type' => '_json:{"1":"Slots","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"真人游戏"}',
			),
			'Soft Games' => array(
				'game_type' => '_json:{"1":"Soft Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Soft Games","2":"真人游戏"}',
			),
			'Table games' => array(
				'game_type' => '_json:{"1":"Table games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Table games","2":"真人游戏"}',
			),
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"真人游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"不明"}',
			),
		);

		$game_types_mg_lapis = array(
			'MG-Lapis Slot Game' => array(
				'game_type' => '_json:{"1":"Slot Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slot Game","2":"老虎机游戏"}',
			),
			'MG-Lapis Classic Slot Game' => array(
				'game_type' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
				'game_type_lang' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
			),
			'MG-Lapis Video Slot Game' => array(
				'game_type' => '_json:{"1":"Video Slot","2":"视频老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slot","2":"视频老虎机"}',
			),
			// 'MG-Lapis Feature Slot Game' => array(
			// 	'game_type' => '_json:{"1":"Feature Slot Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Feature Slot Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Advanced Slot Game' => array(
			// 	'game_type' => '_json:{"1":"Advanced Slot Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Advanced Slot Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Bonus Slot Game' => array(
			// 	'game_type' => '_json:{"1":"Bonus Slot Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Bonus Slot Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Progressive Slot Game' => array(
			// 	'game_type' => '_json:{"1":"Progressive Slot Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Progressive Slot Game","2":"真人游戏"}',
			// ),
			'MG-Lapis Slot Html5 Game' => array(
				'game_type' => '_json:{"1":"Slot Html5 Game","2":"HTML5老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slot Html5 Game","2":"HTML5老虎机游戏"}',
			),
			'MG-Lapis Table Game' => array(
				'game_type' => '_json:{"1":"Table games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table games","2":"桌面游戏"}',
			),
			// 'MG-Lapis Table Gold Game' => array(
			// 	'game_type' => '_json:{"1":"Table Gold Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Table Gold Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Table Premier Game' => array(
			// 	'game_type' => '_json:{"1":"Table Premier Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Table Premier Game","2":"真人游戏"}',
			// ),
			'MG-Lapis Live Dealer Game' => array(
				'game_type' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
			),
			// 'MG-Lapis Live Dealer Html5 Game' => array(
			// 	'game_type' => '_json:{"1":"Live Dealer Html5 Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Live Dealer Html5 Game","2":"真人游戏"}',
			// ),
			'MG-Lapis Video Poker Game' => array(
				'game_type' => '_json:{"1":"Video Poker Game","2":"视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker Game","2":"视频扑克游戏"}',
			),
			// 'MG-Lapis 4 Play Power Poker Game' => array(
			// 	'game_type' => '_json:{"1":"4 Play Power Poker Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"4 Play Power Poker Game","2":"真人游戏"}',
			// ),
			'MG-Lapis Scratch Card Game' => array(
				'game_type' => '_json:{"1":"Scratch Card Game","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratch Card Game","2":"刮刮乐游戏"}',
			),
			// 'MG-Lapis Casual Game' => array(
			// 	'game_type' => '_json:{"1":Casual Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":Casual Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Multi-Hand Gold Series Game' => array(
			// 	'game_type' => '_json:{"1":"Multi-Hand Gold Series Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Multi-Hand Gold Series Game","2":"真人游戏"}',
			// ),
			// 'MG-Lapis Parlor Game' => array(
			// 	'game_type' => '_json:{"1":"Parlor Game","2":"真人游戏"}',
			// 	'game_type_lang' => '_json:{"1":"Parlor Game","2":"真人游戏"}',
			// ),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_vivo = array(
			'VIVO Live Dealer' => array(
				'game_type' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"Live Dealer","2":"真人荷官"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_777 = array(
			'Adult Slot' => array(
				'game_type' => '_json:{"1":"Adult Slot","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"Adult Slot","2":"真人荷官"}',
			),
			'HD Slot' => array(
				'game_type' => '_json:{"1":"HD Slot","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"HD Slot","2":"真人荷官"}',
			),
			'Table Game' => array(
				'game_type' => '_json:{"1":"Table Game","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"真人荷官"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_gsmg = array(
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
			'Slot' => array(
				'game_type' => '_json:{"1":"Slot","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slot","2":"老虎机"}',
			),
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
			// 'PROGRESSIVES' => array(
			// 	'game_type' => '_json:{"1":"PROGRESSIVES","2":"真人荷官"}',
			// 	'game_type_lang' => '_json:{"1":"PROGRESSIVES","2":"真人荷官"}',
			// ),
			'Others' => array(
				'game_type' => '_json:{"1":"Others","2":"其他"}',
				'game_type_lang' => '_json:{"1":"Others","2":"其他"}',
			),
			'Scratch Cards' => array(
				'game_type' => '_json:{"1":"Scratch Card Games","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratch Card Games","2":"刮刮乐游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$game_types_aghg = array(
			'Baccarat' => array(
				'game_type' => '_json:{"1":"Baccarat","2":"百家乐"}',
				'game_type_lang' => '_json:{"1":"Baccarat","2":"百家乐"}',
			),
			'Blackjack' => array(
				'game_type' => '_json:{"1":"Blackjack","2":"二十一点"}',
				'game_type_lang' => '_json:{"1":"Blackjack","2":"二十一点"}',
			),
			'DragonTiger' => array(
				'game_type' => '_json:{"1":"DragonTiger","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"DragonTiger","2":"视频扑克"}',
			),
			'NCBaccarat' => array(
				'game_type' => '_json:{"1":"NC Baccarat","2":"NC 百家乐"}',
				'game_type_lang' => '_json:{"1":"NC Baccarat","2":"NC 百家乐"}',
			),
			'Rng Baccarat' => array(
				'game_type' => '_json:{"1":"Rng Baccarat","2":"RNG 百家乐"}',
				'game_type_lang' => '_json:{"1":"Rng Baccarat","2":"RNG 百家乐"}',
			),
			'Rng BigSam' => array(
				'game_type' => '_json:{"1":"Rng BigSam","2":"Rng BigSam"}',
				'game_type_lang' => '_json:{"1":"Rng BigSam","2":"Rng BigSam"}',
			),
			'Rng Blackjack' => array(
				'game_type' => '_json:{"1":"Rng Blackjack","2":"RNG 二十一点"}',
				'game_type_lang' => '_json:{"1":"Rng Blackjack","2":"RNG 二十一点"}',
			),
			'Rng Carebbean Poker' => array(
				'game_type' => '_json:{"1":"Rng Carebbean Poker","2":"Rng Carebbean Poker"}',
				'game_type_lang' => '_json:{"1":"Rng Carebbean Poker","2":"Rng Carebbean Poker"}',
			),
			'Rng CashBag' => array(
				'game_type' => '_json:{"1":"Rng CashBag","2":"Rng Carebbean Poker"}',
				'game_type_lang' => '_json:{"1":"Rng CashBag","2":"Rng Carebbean Poker"}',
			),
			'Rng CashParty' => array(
				'game_type' => '_json:{"1":"Rng CashParty","2":""Rng CashParty"}',
				'game_type_lang' => '_json:{"1":"Rng CashParty","2":""Rng CashParty"}',
			),
			'Rng Casino War' => array(
				'game_type' => '_json:{"1":"Rng Casino War","2":"Rng Casino War"}',
				'game_type_lang' => '_json:{"1":"Rng Casino War","2":"Rng Casino War"}',
			),
			'Rng CherryMania' => array(
				'game_type' => '_json:{"1":"Rng CherryMania","2":"Rng CherryMania"}',
				'game_type_lang' => '_json:{"1":"Rng CherryMania","2":"Rng CherryMania"}',
			),
			'Rng ClassicJackSorBetter' => array(
				'game_type' => '_json:{"1":"Rng ClassicJackSorBetter","2":"Rng ClassicJackSorBetter"}',
				'game_type_lang' => '_json:{"1":"Rng ClassicJackSorBetter","2":"Rng ClassicJackSorBetter"}',
			),
			'Rng ClassicJokerPoker' => array(
				'game_type' => '_json:{"1":"Rng ClassicJokerPoker","2":"Rng ClassicJokerPoker"}',
				'game_type_lang' => '_json:{"1":"Rng ClassicJokerPoker","2":"Rng ClassicJokerPoker"}',
			),
			'Rng ClassicVideoPoker' => array(
				'game_type' => '_json:{"1":"Rng ClassicVideoPoker","2":"Rng ClassicVideoPoker"}',
				'game_type_lang' => '_json:{"1":"Rng ClassicVideoPoker","2":"Rng ClassicVideoPoker"}',
			),
			'Rng DoubleUpPoker' => array(
				'game_type' => '_json:{"1":"Rng DoubleUpPoker","2":"Rng DoubleUpPoker"}',
				'game_type_lang' => '_json:{"1":"Rng DoubleUpPoker","2":"Rng DoubleUpPoker"}',
			),
			'Rng DoubleUpPokerChance' => array(
				'game_type' => '_json:{"1":"Rng DoubleUpPokerChance","2":"Rng DoubleUpPokerChance"}',
				'game_type_lang' => '_json:{"1":"Rng DoubleUpPokerChance","2":"Rng DoubleUpPokerChance"}',
			),
			'Rng DragonTiger' => array(
				'game_type' => '_json:{"1":"Rng DragonTiger","2":"Rng DragonTiger"}',
				'game_type_lang' => '_json:{"1":"Rng DragonTiger","2":"Rng DragonTiger"}',
			),
			'Rng FrenchRoulette' => array(
				'game_type' => '_json:{"1":"Rng FrenchRoulette","2":"Rng FrenchRoulette"}',
				'game_type_lang' => '_json:{"1":"Rng FrenchRoulette","2":"Rng FrenchRoulette"}',
			),
			'Rng Fruit Slots' => array(
				'game_type' => '_json:{"1":"Rng Fruit Slots","2":"Rng Fruit Slots"}',
				'game_type_lang' => '_json:{"1":"Rng Fruit Slots","2":"Rng Fruit Slots"}',
			),
			'Rng GemFortune' => array(
				'game_type' => '_json:{"1":"Rng GemFortune","2":"Rng GemFortune"}',
				'game_type_lang' => '_json:{"1":"Rng GemFortune","2":"Rng GemFortune"}',
			),
			'Rng GoldenGopher' => array(
				'game_type' => '_json:{"1":"Rng GoldenGopher","2":"Rng GoldenGopher"}',
				'game_type_lang' => '_json:{"1":"Rng GoldenGopher","2":"Rng GoldenGopher"}',
			),
			'Rng GoldenSevens' => array(
				'game_type' => '_json:{"1":"Rng GoldenSevens","2":"Rng GoldenSevens"}',
				'game_type_lang' => '_json:{"1":"Rng GoldenSevens","2":"Rng GoldenSevens"}',
			),
			'Rng JacksorBetter' => array(
				'game_type' => '_json:{"1":"Rng JacksorBetter","2":"Rng JacksorBetter"}',
				'game_type_lang' => '_json:{"1":"Rng JacksorBetter","2":"Rng JacksorBetter"}',
			),
			'Rng JacksorBetterChance' => array(
				'game_type' => '_json:{"1":"Rng JacksorBetterChance","2":"Rng JacksorBetterChance"}',
				'game_type_lang' => '_json:{"1":"Rng JacksorBetterChance","2":"Rng JacksorBetterChance"}',
			),
			'Rng JacksorBetterMulti' => array(
				'game_type' => '_json:{"1":"Rng JacksorBetterMulti","2":"Rng JacksorBetterMulti"}',
				'game_type_lang' => '_json:{"1":"Rng JacksorBetterMulti","2":"Rng JacksorBetterMulti"}',
			),
			'Rng JacksorBetterMultiChance' => array(
				'game_type' => '_json:{"1":"Rng JacksorBetterMultiChance","2":"Rng JacksorBetterMultiChance"}',
				'game_type_lang' => '_json:{"1":"Rng JacksorBetterMultiChance","2":"Rng JacksorBetterMultiChance"}',
			),
			'Rng Jokerpoker' => array(
				'game_type' => '_json:{"1":"Rng Jokerpoker","2":"Rng Jokerpoker"}',
				'game_type_lang' => '_json:{"1":"Rng Jokerpoker","2":"Rng Jokerpoker"}',
			),
			'Rng JokerpokerChance' => array(
				'game_type' => '_json:{"1":"Rng JokerpokerChance","2":""Rng JokerpokerChance"}',
				'game_type_lang' => '_json:{"1":"Rng JokerpokerChance","2":""Rng JokerpokerChance"}',
			),
			'Rng Keno' => array(
				'game_type' => '_json:{"1":"Rng Keno","2":"Rng Keno""}',
				'game_type_lang' => '_json:{"1":"Rng Keno","2":"Rng Keno""}',
			),
			'Rng Letitride Poker' => array(
				'game_type' => '_json:{"1":"Rng Letitride Poker","2":"Rng Letitride Poker"}',
				'game_type_lang' => '_json:{"1":"Rng Letitride Poker","2":"Rng Letitride Poker"}',
			),
			'Rng Lucky888' => array(
				'game_type' => '_json:{"1":"Rng Lucky888","2":""Rng Lucky888"}',
				'game_type_lang' => '_json:{"1":"Rng Lucky888","2":""Rng Lucky888"}',
			),
			'Rng LuckyHarvest' => array(
				'game_type' => '_json:{"1":"Rng LuckyHarvest","2":"Rng LuckyHarvest"}',
				'game_type_lang' => '_json:{"1":"Rng LuckyHarvest","2":"Rng LuckyHarvest"}',
			),
			'Rng LuckyHarvestChance' => array(
				'game_type' => '_json:{"1":"Rng LuckyHarvestChance","2":"Rng LuckyHarvestChance"}',
				'game_type_lang' => '_json:{"1":"Rng LuckyHarvestChance","2":"Rng LuckyHarvestChance"}',
			),
			'Rng MagicForest' => array(
				'game_type' => '_json:{"1":"Rng MagicForest","2":"Rng MagicForest"}',
				'game_type_lang' => '_json:{"1":"Rng MagicForest","2":"Rng MagicForest"}',
			),
			'Rng MangoMania' => array(
				'game_type' => '_json:{"1":"Rng MangoMania","2":"Rng MangoMania"}',
				'game_type_lang' => '_json:{"1":"Rng MangoMania","2":"Rng MangoMania"}',
			),
			'Rng Oasis Poker' => array(
				'game_type' => '_json:{"1":"Rng Oasis Poker","2":"Rng Oasis Poker"}',
				'game_type_lang' => '_json:{"1":"Rng Oasis Poker","2":"Rng Oasis Poker"}',
			),
			'Rng Paigowpoker' => array(
				'game_type' => '_json:{"1":"Rng Paigowpoker","2":"Rng Paigowpoker"}',
				'game_type_lang' => '_json:{"1":"Rng Paigowpoker","2":"Rng Paigowpoker"}',
			),
			'Rng PinkDiamond' => array(
				'game_type' => '_json:{"1":"Rng PinkDiamond","2":"Rng PinkDiamond"}',
				'game_type_lang' => '_json:{"1":"Rng PinkDiamond","2":"Rng PinkDiamond"}',
			),
			'Rng PinkDiamondChance' => array(
				'game_type' => '_json:{"1":"Rng PinkDiamondChance","2":"Rng PinkDiamondChance"}',
				'game_type_lang' => '_json:{"1":"Rng PinkDiamondChance","2":"Rng PinkDiamondChance"}',
			),
			'Rng Reddog' => array(
				'game_type' => '_json:{"1":"Rng Reddog","2":"Rng Reddog"}',
				'game_type_lang' => '_json:{"1":"Rng Reddog","2":"Rng Reddog"}',
			),
			'Rng RoadToRiches' => array(
				'game_type' => '_json:{"1":"Rng RoadToRiches","2":"Rng RoadToRiches"}',
				'game_type_lang' => '_json:{"1":"Rng RoadToRiches","2":"Rng RoadToRiches"}',
			),
			'Rng Roulette' => array(
				'game_type' => '_json:{"1":"Rng Roulette","2":"Rng 轮盘"}',
				'game_type_lang' => '_json:{"1":"Rng Roulette","2":"Rng 轮盘"}',
			),
			'Rng SkillSlot' => array(
				'game_type' => '_json:{"1":"Rng SkillSlot","2":"Rng SkillSlot"}',
				'game_type_lang' => '_json:{"1":"Rng SkillSlot","2":"Rng SkillSlot"}',
			),
			'Rng Slots' => array(
				'game_type' => '_json:{"1":"Rng Slots","2":"Rng 老虎机"}',
				'game_type_lang' => '_json:{"1":"Rng Slots","2":"Rng 老虎机"}',
			),
			'Rng Slots Chance' => array(
				'game_type' => '_json:{"1":"Rng Slots Chance","2":"Rng Slots Chance"}',
				'game_type_lang' => '_json:{"1":"Rng Slots Chance","2":"Rng Slots Chance"}',
			),
			'Rng Snapjax' => array(
				'game_type' => '_json:{"1":"Rng Snapjax","2":"Rng Snapjax"}',
				'game_type_lang' => '_json:{"1":"Rng Snapjax","2":"Rng Snapjax"}',
			),
			'Rng SqzBaccarat' => array(
				'game_type' => '_json:{"1":"Rng SqzBaccarat","2":"Rng SqzBaccarat"}',
				'game_type_lang' => '_json:{"1":"Rng SqzBaccarat","2":"Rng SqzBaccarat"}',
			),
			'Rng ThreeCardPoker' => array(
				'game_type' => '_json:{"1":"Rng ThreeCardPoker","2":"Rng ThreeCardPoker"}',
				'game_type_lang' => '_json:{"1":"Rng ThreeCardPoker","2":"Rng ThreeCardPoker"}',
			),
			'Rng Videopoker' => array(
				'game_type' => '_json:{"1":"Rng Videopoker","2":"Rng 视频扑克"}',
				'game_type_lang' => '_json:{"1":"Rng Videopoker","2":"Rng 视频扑克"}',
			),
			'Rng VideopokerChance' => array(
				'game_type' => '_json:{"1":"Rng VideopokerChance","2":"Rng VideopokerChance"}',
				'game_type_lang' => '_json:{"1":"Rng VideopokerChance","2":"Rng VideopokerChance"}',
			),
			'Roulette' => array(
				'game_type' => '_json:{"1":"Roulette","2":"轮盘"}',
				'game_type_lang' => '_json:{"1":"Roulette","2":"轮盘"}',
			),
			'Sic Bo' => array(
				'game_type' => '_json:{"1":"Sic Bo","2":"骰宝"}',
				'game_type_lang' => '_json:{"1":"Sic Bo","2":"骰宝"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
		);

		$this->db->trans_start();

		foreach ($game_types_pt as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', 1)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_nt as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', NT_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_mg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', MG_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_inteplay as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', INTEPLAY_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_onesgame as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', ONESGAME_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gspt as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', GSPT_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gsag as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', GSAG_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_ab as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', AB_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_bs as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', BS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_ttg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', AGIN_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_mg_lapis as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', LAPIS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_vivo as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', VIVO_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_777 as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', SEVEN77_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gsmg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', GSMG_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_aghg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id',AGHG_API)
					 ->update('game_type', $game_type_data);

		}

		$this->db->trans_complete();
	}

	public function down() {
	}
}
