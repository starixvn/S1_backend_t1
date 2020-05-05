<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ebet_game_description_20160704 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		
		$this->load->model('game_type_model');
		$this->db->trans_start();
		$game_type_id = $this->game_type_model->getUnknownGameType(EBET_API)->id;

		$this->db->insert_batch('game_description', array(
			array(
				'game_platform_id' => EBET_API,
				'game_type_id' => $game_type_id,
				'game_code' => '1',
				'external_game_id' => '1',
				'game_name' => '百家乐',
				'english_name' => '百家乐',
			),
			array(
				'game_platform_id' => EBET_API,
				'game_type_id' => $game_type_id,
				'game_code' => '2',
				'external_game_id' => '2',
				'game_name' => '龙虎',
				'english_name' => '龙虎',
			),
			array(
				'game_platform_id' => EBET_API,
				'game_type_id' => $game_type_id,
				'game_code' => '3',
				'external_game_id' => '3',
				'game_name' => '骰宝',
				'english_name' => '骰宝',
			),
			array(
				'game_platform_id' => EBET_API,
				'game_type_id' => $game_type_id,
				'game_code' => '4',
				'external_game_id' => '4',
				'game_name' => '轮盘',
				'english_name' => '轮盘',
			),
			array(
				'game_platform_id' => EBET_API,
				'game_type_id' => $game_type_id,
				'game_code' => '5',
				'external_game_id' => '5',
				'game_name' => '水果机',
				'english_name' => '水果机',
			),
		));
		$this->db->trans_complete();
	}

	public function down() {

		$game_platform_id = EBET_API;

		$this->db->trans_start();

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->where('game_code !=', 'unknown');
		$this->db->delete('game_description');

		$this->db->trans_complete();
	}
}
