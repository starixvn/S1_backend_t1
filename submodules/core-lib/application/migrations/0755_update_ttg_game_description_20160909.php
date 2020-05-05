<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ttg_game_description_20160909 extends CI_Migration {

	public function up() {
		// $this->load->model('game_type_model');

		// $game_type_id = $this->game_type_model->checkGameType(TTG_API, 'Slot');

		// $this->db->insert_batch('game_description', array(
		// 	array(
		// 		'game_platform_id' => TTG_API,
		// 		'game_type_id' => $game_type_id,
		// 		'game_code' => 'BerryBlastPlus',
		// 		'external_game_id' => 'BerryBlastPlus',
		// 		'game_name' => 'Berry Blast Plus',
		// 		'english_name' => 'Berry Blast Plus',
		// 		'attributes' => json_encode(array(
		// 			'gameId' => 1038,
		// 			'gameType' => 0,
		// 		)),
		// 	),
		// ));

	}

	public function down() {

		// $game_platform_id = TTG_API;

		// $this->db->where('game_platform_id', $game_platform_id);
		// $this->db->where('game_code', 'BerryBlastPlus');
		// $this->db->delete('game_description');

	}

}