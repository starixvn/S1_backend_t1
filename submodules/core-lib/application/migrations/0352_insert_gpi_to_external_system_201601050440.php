<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_gpi_to_external_system_201601050440 extends CI_Migration {

	private $esl_table = 'external_system_list';
	private $es_table = 'external_system';
	private $game_table = 'game';

	public function up() {
		//GPI
		// $this->db->insert($this->esl_table,
		// 	array(
		// 		"id" => GPI_API,
		// 		"system_name" => "GPI_API",
		// 		"system_code" => "GPI",
		// 		'system_type' => SYSTEM_GAME_API,
		// 		"live_mode" => 0,
		// 		"class_name" => "game_api_gpi",
		// 		"local_path" => "game_platform",
		// 		"manager" => "game_platform_manager",
		// 	)
		// );

		// //check config first
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(GPI_API, $sys)) {
		// 	$this->db->insert($this->es_table, array(
		// 		"id" => GPI_API, "system_name" => "GPI_API", "system_code" => 'GPI',
		// 		'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 		"class_name" => "game_api_gpi", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));

		// 	$this->db->insert($this->game_table, array('gameId' => GPI_API, 'game' => 'GPI'));
		// }
	}

	public function down() {
		// $this->db->delete($this->esl_table, array('id' => GPI_API));
		// $this->db->delete($this->es_table, array('id' => GPI_API));
		// $this->db->delete($this->game_table, array('gameId' => GPI_API));
	}
}
