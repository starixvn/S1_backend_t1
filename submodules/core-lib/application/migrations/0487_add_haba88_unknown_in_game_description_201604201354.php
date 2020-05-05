<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_haba88_unknown_in_game_description_201604201354 extends CI_Migration {
	const FLAG_TRUE = 1;
	private $tableName = 'game_description';

	public function up() {
		// $this->db->trans_start();
		// $data = array(

		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'video slot unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '86',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Baccarat unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '87',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Blackjack unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '88',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Casino Poker unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '89',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Classic Slots unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '90',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Gamble unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '91',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Roulette unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '92',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Sic Bo unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '93',
		// 	),
		// 	array(
		// 		'game_platform_id' => HB_API,
		// 		'game_name' => 'haba88.unknown',
		// 		'english_name' => 'Video Poker unknown',
		// 		'external_game_id' => 'unknown',
		// 		'game_code' => 'unknown',
		// 		'game_type_id' => '93',
		// 	),
		// );

		// $this->db->insert_batch('game_description', $data);
		// $this->db->trans_complete();
	}

	public function down() {
		// $game_name = array('haba88.unknown');
		// $this->db->where_in('game_name', $game_name);
		// $this->db->delete($this->tableName);
	}
}
