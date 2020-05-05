<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_types_names_201610121408 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$game_types_bbin = array(
			'Sports Game' => array(
				'game_type' => '_json:{"1":"Sports Game","2":"手机游戏"}',
				'game_type_lang' => '_json:{"1":"Sports Game","2":"手机游戏"}',
			),
			'3D Hall Game' => array(
				'game_type' => '_json:{"1":"3D Hall Game","2":"3D大厅游戏"}',
				'game_type_lang' => '_json:{"1":"3D Hall Game","2":"3D大厅游戏"}',
			),
			'Casino Game' => array(
				'game_type' => '_json:{"1":"Casino Games","2":"赌场游戏"}',
				'game_type_lang' => '_json:{"1":"Casino Games","2":"赌场游戏"}',
			),
			'Live Game' => array(
				'game_type' => '_json:{"1":"Live Game","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Game","2":"真人游戏"}',
			),
			'Lottery Game' => array(
				'game_type' => '_json:{"1":"Lottery Game","2":"彩票游戏"}',
				'game_type_lang' => '_json:{"1":"Lottery Game","2":"彩票游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_gd = array(
			'Video Pokers' => array(
				'game_type' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
			),
			'Video Slots' => array(
				'game_type' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
			),
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
			'Slot Games' => array(
				'game_type' => '_json:{"1":"Slot Games","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slot Games","2":"老虎机游戏"}',
			),
			'Gamble' => array(
				'game_type' => '_json:{"1":"Gamble","2":"赌博"}',
				'game_type_lang' => '_json:{"1":"Gamble","2":"赌博"}',
			),
			'GD Live Dealer' => array(
				'game_type' => '_json:{"1":"GD Live Dealer","2":"真人荷官"}',
				'game_type_lang' => '_json:{"1":"GD Live Dealer","2":"真人荷官"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_beteast= array(
			'BETEAST EGAME CASINO' => array(
				'game_type' => '_json:{"1":"EGAME CASINO","2":"电子游戏"}',
				'game_type_lang' => '_json:{"1":"EGAME CASINO","2":"电子游戏"}',
			),
			'BETEAST LIVE CASINO' => array(
				'game_type' => '_json:{"1":"LIVE CASINO","2":" 真人赌场"}',
				'game_type_lang' => '_json:{"1":"LIVE CASINO","2":" 真人赌场"}',
			),
			'BETEAST UNKNOWN GAME' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_opus = array(
			'Card' => array(
				'game_type' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
			),
			'Slot' => array(
				'game_type' => '_json:{"1":"Slot","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slot","2":"老虎机"}',
			),
			'Table' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"老虎机"}',
			),
			'Jackpot' => array(
				'game_type' => '_json:{"1":"Jackpot","2":"累计奖池"}',
				'game_type_lang' => '_json:{"1":"Jackpot","2":"累计奖池"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_kenogame= array(
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_qt= array(
			'Scratchcard' => array(
				'game_type' => '_json:{"1":"Scratchcard Game","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratchcard Game","2":"刮刮乐游戏"}',
			),
			'Slot' => array(
				'game_type' => '_json:{"1":"Slot","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slot","2":"老虎机"}',
			),
			'Table Game' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
		);

		$game_types_gameplay = array(
			'gameplay_slots' => array(
				'game_type' => '_json:{"1":"Slot","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slot","2":"老虎机"}',
			),
			'gameplay_table' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'gameplay.unknown' => array(
				'game_type' => '_json:{"1":"unknown,"2":"不明"}',
				'game_type_lang' => '_json:{"1":"unknown","2":"不明"}',
			),
			'gameplay_sbtech_arcades' => array(
				'game_type' => '_json:{"1":"SBTech Arcades","2":"街机游戏"}',
				'game_type_lang' => '_json:{"1":"SBTech Arcades","2":"街机游戏"}',
			),
			'gameplay_sbtech_cardgames' => array(
				'game_type' => '_json:{"1":"SBTech Card Games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"SBTech Card Games","2":"纸牌游戏"}',
			),
			'gameplay_sbtech_slots' => array(
				'game_type' => '_json:{"1":"SBTech Slots","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"SBTech Slots","2":"老虎机"}',
			),
			'gameplay_sbtech_table_games' => array(
				'game_type' => '_json:{"1":"SBTech Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"SBTech Table Games","2":"桌面游戏"}',
			),
			'gameplay_sbtech_video_poker' => array(
				'game_type' => '_json:{"1":"SBTech Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"SBTech Video Poker","2":"视频扑克"}',
			),
			'gameplay_rslots' => array(
				'game_type' => '_json:{"1":"RSlots","2":"R老虎机"}',
				'game_type_lang' => '_json:{"1":"RSlots","2":"R老虎机"}',
			),
			'gameplay.sbtech.unknown' => array(
				'game_type' => '_json:{"1":"SBTech Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"SBTech Unknown","2":"不明"}',
			),
		);

		$game_types_agin = array(
			'HSR' => array(
				'game_type' => '_json:{"1":"Hunter","2":"猎人"}',
				'game_type_lang' => '_json:{"1":"Hunter","2":"猎人"}',
			),
			'BR' => array(
				'game_type' => '_json:{"1":"Live","2":"现场"}',
				'game_type_lang' => '_json:{"1":"Live","2":"现场"}',
			),
			'EBR' => array(
				'game_type' => '_json:{"1":"EGame","2":"电子游戏"}',
				'game_type_lang' => '_json:{"1":"EGame","2":"电子游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
		);

		$this->db->trans_start();

		foreach ($game_types_bbin as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', BBIN_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gd as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', GD_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_beteast as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', BETEAST_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_opus as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', OPUS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_kenogame as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', KENOGAME_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_qt as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', QT_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gameplay as $game_type_lang => $game_type_data) {

			$this->db->where('game_type_lang', $game_type_lang)
					 ->where('game_platform_id', GAMEPLAY_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_agin as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', AGIN_API)
					 ->update('game_type', $game_type_data);

		}

		$this->db->trans_complete();
	}

	public function down() {
	}
}
