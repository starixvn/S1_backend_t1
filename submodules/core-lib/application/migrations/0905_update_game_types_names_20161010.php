<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_types_names_20161010 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		$game_types_oneworks = array(
			'Oneworks Mobile Game' => array(
				'game_type' => '_json:{"1":"Mobile Game","2":"手机游戏"}',
				'game_type_lang' => '_json:{"1":"Mobile Game","2":"手机游戏"}',
			),
			'Oneworks Number Game' => array(
				'game_type' => '_json:{"1":"Number Game","2":"百练赛"}',
				'game_type_lang' => '_json:{"1":"Number Game","2":"百练赛"}',
			),
			'Oneworks Sportsbook Games' => array(
				'game_type' => '_json:{"1":"Sportsbook Games","2":"体育"}',
				'game_type_lang' => '_json:{"1":"Sportsbook Games","2":"体育"}',
			),
			'Oneworks Virtual Sport Game' => array(
				'game_type' => '_json:{"1":"Virtual Sport Game","2":"虚拟体育"}',
				'game_type_lang' => '_json:{"1":"Virtual Sport Game","2":"虚拟体育"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_isb = array(
			'Isoftbet Branded Slot Game' => array(
				'game_type' => '_json:{"1":"Isoftbet Branded Slot Game","2":"品牌老虎机游戏}',
				'game_type_lang' => '_json:{"1":"Branded Slot Game","2":"品牌老虎机游戏}',
			),
			'Isoftbet Branded Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Branded Slots Html5 Game","2":"百练赛"}',
				'game_type_lang' => '_json:{"1":"Branded Slots Html5 Game","2":"HTML5品牌老虎机游戏"}',
			),
			'Isoftbet Mechanical Slot Game' => array(
				'game_type' => '_json:{"1":Mechanical Slot Game","2":"转轴老虎机游戏"}',
				'game_type_lang' => '_json:{"1":Mechanical Slot Game","2":"转轴老虎机游戏"}',
			),
			'Isoftbet Mechanical Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}',
			),
			'Isoftbet Slots Game' => array(
				'game_type' => '_json:{"1":"Slots Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slots Game","2":"老虎机游戏"}',
			),
			'Isoftbet Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}',
			),
			'Isoftbet Table Game' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Isoftbet Table Html5 Game' => array(
				'game_type' => '_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}',
			),
			'Isoftbet Video Poker Game' => array(
				'game_type' => '_json:{"1":"Video Poker Game","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker Game","2":"视频扑克"}',
			),
			'Isoftbet Video Poker Html5 Game' => array(
				'game_type' => '_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}',
			),
		);

		$game_types_isb = array(
			'Isoftbet Branded Slot Game' => array(
				'game_type' => '_json:{"1":"Branded Slot Game","2":"品牌老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Branded Slot Game","2":"品牌老虎机游戏"}',
			),
			'Isoftbet Branded Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Branded Slots Html5 Game","2":"HTML5品牌老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Branded Slots Html5 Game","2":"HTML5品牌老虎机游戏"}',
			),
			'Isoftbet Mechanical Slot Game' => array(
				'game_type' => '_json:{"1":"Mechanical Slot Game","2":"转轴老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Mechanical Slot Game","2":"转轴老虎机游戏"}',
			),
			'Isoftbet Mechanical Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Mechanical Slots Html5 Game","2":"HTML5转轴老虎机游戏"}',
			),
			'Isoftbet Slots Game' => array(
				'game_type' => '_json:{"1":"Slots Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slots Game","2":"老虎机游戏"}',
			),
			'Isoftbet Slots Html5 Game' => array(
				'game_type' => '_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slots Html5 Game","2":"HTML5老虎机游戏"}',
			),
			'Isoftbet Table Game' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'Isoftbet Table Html5 Game' => array(
				'game_type' => '_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Html5 Game","2":"HTML5桌面游戏"}',
			),
			'Isoftbet Video Poker Game' => array(
				'game_type' => '_json:{"1":"Video Poker Game","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker Game","2":"视频扑克"}',
			),
			'Isoftbet Video Poker Html5 Game' => array(
				'game_type' => '_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker Html5 Game","2":"HTML5视频扑克游戏"}',
			),
		);

		$game_types_fg = array(
			'NYX Slots Game' => array(
				'game_type' => '_json:{"1":"NYX Slot Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"NYX Slot Game","2":"老虎机游戏"}',
			),
			'NYX Table Game' => array(
				'game_type' => '_json:{"1":"NYX Table","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"NYX Table","2":"桌面游戏"}',
			),
			'NYX Slots Mini Games' => array(
				'game_type' => '_json:{"1":"NYX Slots Mini Games","2":"迷你老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"NYX Slots Mini Games","2":"迷你老虎机游戏"}',
			),
			'PlayNGO Grid Slot Game' => array(
				'game_type' => '_json:{"1":"PlayNGO Grid Slot Game","2":"格子老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Grid Slot Game","2":"格子老虎机游戏"}',
			),
			'PlayNGO Grid Slot Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Grid Slot Mobile Games","2":"手机格子老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Grid Slot Mobile Games","2":"手机格子老虎机游戏"}',
			),
			'PlayNGO Mini Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Mini Games","2":"迷你游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Mini Games","2":"迷你游戏"}',
			),
			'PlayNGO Other Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Other Games","2":"其他游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Other Games","2":"其他游戏"}',
			),
			'PlayNGO Other Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Other Mobile Games","2":"其他手机游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Other Mobile Games","2":"其他手机游戏"}',
			),
			'PlayNGO Scratch Card Game' => array(
				'game_type' => '_json:{"1":"PlayNGO Scratch Card Game","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Scratch Card Game","2":"刮刮乐游戏"}',
			),
			'PlayNGO Scratch Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Scratch Mobile Games","2":"手机刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Scratch Mobile Games","2":"手机刮刮乐游戏"}',
			),
			'PlayNGO Slot Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Slot Mobile Games","2":"手机老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Slot Mobile Games","2":"手机老虎机游戏"}',
			),
			'PlayNGO Slots Game' => array(
				'game_type' => '_json:{"1":"PlayNGO Slots Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Slots Game","2":"老虎机游戏"}',
			),
			'PlayNGO Table Game' => array(
				'game_type' => '_json:{"1":"PlayNGO Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Table Game","2":"桌面游戏"}',
			),
			'PlayNGO Table Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Table Mobile Games","2":"手机桌面游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Table Mobile Games","2":"手机桌面游戏"}',
			),
			'PlayNGO Video Poker Game' => array(
				'game_type' => '_json:{"1":"PlayNGO Video Poker Game","2":"视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Video Poker Game","2":"视频扑克游戏"}',
			),
			'PlayNGO Video Poker Mobile Games' => array(
				'game_type' => '_json:{"1":"PlayNGO Video Poker Mobile Games","2":"手机视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"PlayNGO Video Poker Mobile Games","2":"手机视频扑克游戏"}',
			),
			'Playson Slots Game' => array(
				'game_type' => '_json:{"1":"Playson Slots Game","2":"老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Slots Game","2":"老虎机游戏"}',
			),
			'Playson Slots Mobile Game' => array(
				'game_type' => '_json:{"1":"Playson Slots Mobile Game","2":"手机老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Slots Mobile Game","2":"手机老虎机游戏"}',
			),
			'Playson Table Game' => array(
				'game_type' => '_json:{"1":"Playson Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Table Game","2":"桌面游戏"}',
			),
			'Playson Table Mobile Game' => array(
				'game_type' => '_json:{"1":"Playson Table Mobile Game","2":"手机桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Table Mobile Game","2":"手机桌面游戏"}',
			),
			'Playson Video Poker Game' => array(
				'game_type' => '_json:{"1":"Playson Video Poker Game","2":"视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Video Poker Game","2":"视频扑克游戏"}',
			),
			'Playson Video Poker Mobile Game' => array(
				'game_type' => '_json:{"1":"Playson Video Poker Mobile Game","2":"手机视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Playson Video Poker Mobile Game","2":"手机视频扑克游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_gamesos = array(
			'gamesos.unknown' => array(
				'game_type' => '_json:{"1":"Unkown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unkown","2":"不明"}',
			),
			'gamesos_arcades_games' => array(
				'game_type' => '_json:{"1":"Arcade Games","2":"街机游戏"}',
				'game_type_lang' => '_json:{"1":"Arcade Games","2":"街机游戏"}',
			),
			'gamesos_card_games' => array(
				'game_type' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Card Games","2":"纸牌游戏"}',
			),
			'gamesos_mobile_card_games' => array(
				'game_type' => '_json:{"1":"Mobile Card Games","2":"手机纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Mobile Card Games","2":"手机纸牌游戏"}',
			),
			'gamesos_mobile_slots' => array(
				'game_type' => '_json:{"1":"Slot Mobile Game","2":"手机老虎机游戏"}',
				'game_type_lang' => '_json:{"1":"Slot Mobile Game","2":"手机老虎机游戏"}',
			),
			'gamesos_mobile_table_games' => array(
				'game_type' => '_json:{"1":"Table Mobile Game","2":"手机桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Mobile Game","2":"手机桌面游戏"}',
			),
			'gamesos_mobile_video_poker' => array(
				'game_type' => '_json:{"1":"Video Poker Mobile Game","2":"手机视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker Mobile Game","2":"手机视频扑克游戏"}',
			),
			'gamesos_others_games' => array(
				'game_type' => '_json:{"1":"Other Games","2":"其他游戏"}',
				'game_type_lang' => '_json:{"1":"Other Games","2":"其他游戏"}',
			),
			'gamesos_popular_games' => array(
				'game_type' => '_json:{"1":"Popular Games","2":"流行"}',
				'game_type_lang' => '_json:{"1":"Popular Games","2":"流行"}',
			),
			'gamesos_table_games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
			'gamesos_video_pokers_games' => array(
				'game_type' => '_json:{"1":"Video Poker Game","2":"视频扑克游戏"}',
				'game_type_lang' => '_json:{"1":"Video Poker Game","2":"视频扑克游戏"}',
			),
		);

		$game_types_entwine = array(
			'Live Game' => array(
				'game_type' => '_json:{"1":"Live Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏"}',
			),
			'unknown' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_hb = array(
			'Baccarat' => array(
				'game_type' => '_json:{"1":"Baccarat","2":"百家乐"}',
				'game_type_lang' => '_json:{"1":"Baccarat","2":"百家乐"}',
			),
			'Blackjack' => array(
				'game_type' => '_json:{"1":"Blackjack","2":"二十一点"}',
				'game_type_lang' => '_json:{"1":"Blackjack","2":"二十一点"}',
			),
			'Casino Poker' => array(
				'game_type' => '_json:{"1":"Casino Poker","2":"赌场扑克"}',
				'game_type_lang' => '_json:{"1":"Casino Poker","2":"赌场扑克"}',
			),
			'Classic Slots' => array(
				'game_type' => '_json:{"1":"Classic Slots","2":"经典老虎机"}',
				'game_type_lang' => '_json:{"1":"Classic Slots","2":"经典老虎机"}',
			),
			'Gamble' => array(
				'game_type' => '_json:{"1":"Gamble","2":"赌博"}',
				'game_type_lang' => '_json:{"1":"Gamble","2":"赌博"}',
			),
			'Roulette' => array(
				'game_type' => '_json:{"1":"Roulette","2":"轮盘"}',
				'game_type_lang' => '_json:{"1":"Roulette","2":"轮盘"}',
			),
			'Sic Bo' => array(
				'game_type' => '_json:{"1":"Sic Bo","2":"骰宝"}',
				'game_type_lang' => '_json:{"1":"Sic Bo","2":"骰宝"}',
			),
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
			'Video Slots' => array(
				'game_type' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
				'game_type_lang' => '_json:{"1":"Video Slots","2":"视频老虎机"}',
			),
		);

		$this->db->trans_start();

		foreach ($game_types_oneworks as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', ONEWORKS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_isb as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', ISB_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_fg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', FG_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_gamesos as $game_type_lang => $game_type_lang_data) {

			$this->db->where('game_type_lang', $game_type_lang)
					 ->where('game_platform_id', GAMESOS_API)
					 ->update('game_type', $game_type_lang_data);

		}

		foreach ($game_types_entwine as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', ENTWINE_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_hb as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', HB_API)
					 ->update('game_type', $game_type_data);

		}


		$this->db->trans_complete();
	}

	public function down() {
	}
}
