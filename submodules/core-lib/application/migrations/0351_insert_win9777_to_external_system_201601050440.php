<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_win9777_to_external_system_201601050440 extends CI_Migration {

	private $esl_table = 'external_system_list';
	private $es_table = 'external_system';
	private $game_table = 'game';

	public function up() {
		//WIN9777
		// $this->db->insert($this->esl_table,
		// 	array(
		// 		"id" => WIN9777_API,
		// 		"system_name" => "WIN9777_API",
		// 		"system_code" => "WIN9777",
		// 		'system_type' => SYSTEM_GAME_API,
		// 		"live_mode" => 0,
		// 		"class_name" => "game_api_win9777",
		// 		"local_path" => "game_platform",
		// 		"manager" => "game_platform_manager",
		// 	)
		// );

		// //check config first
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(WIN9777_API, $sys)) {
		// 	$this->db->insert($this->es_table,
		// 		array(
		// 			"id" => WIN9777_API,
		// 			"system_name" => "WIN9777_API",
		// 			"system_code" => 'WIN9777',
		// 			'system_type' => SYSTEM_GAME_API,
		// 			"live_mode" => 0,
		// 			"class_name" => "game_api_win9777",
		// 			"local_path" => "game_platform",
		// 			"manager" => "game_platform_manager",
		// 		)
		// 	);
		// 	$this->db->insert($this->game_table, array('gameId' => WIN9777_API, 'game' => 'WIN9777'));
		// }
	}

	public function down() {
		// $this->db->delete($this->esl_table, array('id' => WIN9777_API));
		// $this->db->delete($this->es_table, array('id' => WIN9777_API));
		// $this->db->delete($this->game_table, array('gameId' => WIN9777_API));
	}
}
