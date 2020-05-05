<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_types_names_201611291052 extends CI_Migration {


	public function up(){
		$game_types_pt = array(
			'_json:{"1":"Table Game","2":"百家乐"}' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
			'_json:{"1":"Live Games","2":"赌场游戏"}' => array(
				'game_type' => '_json:{"1":"Live Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏"}',
			),
			'_json:{"1":"Slot Machines","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Slot Machines","2":"Slot Machines"}',
				'game_type_lang' => '_json:{"1":"Slot Machines","2":"Slot Machines"}',
			),
			'_json:{"1":"Scratchcards","2":"彩票游戏"}' => array(
				'game_type' => '_json:{"1":"Scratchcards","2":"刮刮乐游戏"}',
				'game_type_lang' => '_json:{"1":"Scratchcards","2":"刮刮乐游戏"}',
			),
			'_json:{"1":"Video Pokers","2":"彩票游戏"}' => array(
				'game_type' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Pokers","2":"视频扑克"}',
			),
		);

		$game_types_onesgame= array(
			'_json:{"1":"Slots","2":"不明"}' => array(
				'game_type' => '_json:{"1":"Slots","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"老虎机"}',
			),
			'_json:{"1":"Table","2":"不明"}' => array(
				'game_type' => '_json:{"1":"Table Game","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Game","2":"桌面游戏"}',
			),
		);

		$game_types_gspt= array(
			'_json:{"1":"Live Dealer","2":"真人荷官"}' => array(
				'game_type' => '_json:{"1":"Live Dealer","2":"Live Dealer"}',
				'game_type_lang' => '_json:{"1":"Live Dealer","2":"Live Dealer"}',
			),
			'Table Games' => array(
				'game_type' => '_json:{"1":"Table Games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table Games","2":"桌面游戏"}',
			),
		);

		$game_types_gsag = array(
			'_json:{"1":"EGame","2":"视频扑克"}' => array(
				'game_type' => '_json:{"1":"EGame","2":"电子游戏"}',
				'game_type_lang' => '_json:{"1":"EGame","2":"电子游戏"}',
			),
			'_json:{"1":"Live Games","2":"R老虎机"}' => array(
				'game_type' => '_json:{"1":"Live Games","2":"真人游戏"}',
				'game_type_lang' => '_json:{"1":"Live Games","2":"真人游戏"}',
			),
		);

		$game_types_bs = array(
			'_json:{"1":"Video Poker","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
			'_json:{"1":"Multihand Poker","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Multihand Poker","2":"Multihand Poker"}',
				'game_type_lang' => '_json:{"1":"Multihand Poker","2":"Multihand Poker"}',
			),
			'_json:{"1":"Soft Games","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Soft Games","2":"Soft Games"}',
				'game_type_lang' => '_json:{"1":"Soft Games","2":"Soft Games"}',
			),
			'_json:{"1":"Slots","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Slots","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"老虎机"}',
			),
			'_json:{"1":"Pyramid Poker","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Pyramid Poker","2":"Pyramid Poker"}',
				'game_type_lang' => '_json:{"1":"Pyramid Poker","2":"Pyramid Poker"}',
			),
			'_json:{"1":"Table","2":"真人游戏"}' => array(
				'game_type' => '_json:{"1":"Table","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table","2":"桌面游戏"}',
			),
			'_json:{"1":"Video Poker","2":"不明"}' => array(
				'game_type' => '_json:{"1":"Unknown","2":"不明"}',
				'game_type_lang' => '_json:{"1":"Unknown","2":"不明"}',
			),
		);

		$game_types_ttg = array(
			'Card games' => array(
				'game_type' => '_json:{"1":"Card games","2":"纸牌游戏"}',
				'game_type_lang' => '_json:{"1":"Card games","2":"纸牌游戏"}',
			),
			'Slot' => array(
				'game_type' => '_json:{"1":"Slots","2":"老虎机"}',
				'game_type_lang' => '_json:{"1":"Slots","2":"老虎机"}',
			),
			'Soft games' => array(
				'game_type' => '_json:{"1":"Soft Games","2":"Soft Games"}',
				'game_type_lang' => '_json:{"1":"Soft Games","2":"Soft Games"}',
			),
			'Table games' => array(
				'game_type' => '_json:{"1":"Table games","2":"桌面游戏"}',
				'game_type_lang' => '_json:{"1":"Table games","2":"桌面游戏"}',
			),
			'Video Poker' => array(
				'game_type' => '_json:{"1":"Video Poker","2":"视频扑克"}',
				'game_type_lang' => '_json:{"1":"Video Poker","2":"视频扑克"}',
			),
		);

		$game_types_mg_lapis = array(
			'_json:{"1":"Classic Slot","2":"经典老虎机"}' => array(
				'game_type' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
				'game_type_lang' => '_json:{"1":"Classic Slot","2":"经典老虎机"}',
			),
		);

		$game_types_aghg = array(
			'_json:{"1":"DragonTiger","2":"视频扑克"}' => array(
				'game_type' => '_json:{"1":"DragonTiger","2":"DragonTiger"}',
				'game_type_lang' => '_json:{"1":"DragonTiger","2":"DragonTiger"}',
			),
			'_json:{"1":"Rng CashBag","2":"Rng Carebbean Poker"}' => array(
				'game_type' => '_json:{"1":"Rng CashBag","2":"Rng CashBag"}',
				'game_type_lang' => '_json:{"1":"Rng CashBag","2":"Rng CashBag"}',
			),
		);


		$this->db->trans_start();

		foreach ($game_types_pt as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', PT_API)
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

		foreach ($game_types_bs as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', BS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_ttg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', TTG_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_mg_lapis as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id', LAPIS_API)
					 ->update('game_type', $game_type_data);

		}

		foreach ($game_types_aghg as $game_type => $game_type_data) {

			$this->db->where('game_type', $game_type)
					 ->where('game_platform_id',AGHG_API)
					 ->update('game_type', $game_type_data);

		}

		$this->db->trans_complete();
	}

	public function down(){

	}

}