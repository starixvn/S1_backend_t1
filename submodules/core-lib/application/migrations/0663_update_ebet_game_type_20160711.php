<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ebet_game_type_20160711 extends CI_Migration {

	public function up() {
		// $this->load->model('game_type_model');
		// $this->db->trans_start();
		// $game_descriptions = $this->db->get_where('game_description', array('game_platform_id' => EBET_API, 'game_code !=' => 'unknown'))->result_array();
		// foreach ($game_descriptions as &$game_description) {
		// 	$game_description['game_type_id'] = $this->game_type_model->createGameType($game_description['game_name'], EBET_API);
		// }
		// $this->db->update_batch('game_description', $game_descriptions, 'game_type_id');
		// $this->db->trans_complete();
	}

	public function down() {

		// $game_platform_id = EBET_API;

		// $this->db->trans_start();

		// $this->db->where('game_platform_id', $game_platform_id);
		// $this->db->where('game_type !=', 'unknown');
		// $this->db->delete('game_type');

		// $this->db->trans_complete();
	}
}
