<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_keno_to_external_system_201511170559 extends CI_Migration {

	private $esl_table = 'external_system_list';
	private $es_table = 'external_system';
	private $game_table = 'game';

	public function up() {
		//keno
		// $this->db->insert($this->esl_table, array(
		// 	"id" => LB_API, "system_name" => "LB_API", "system_code" => 'LB',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_keno", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));

		//check config first
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(LB_API, $sys)) {
		// 	$this->db->insert($this->es_table, array(
		// 		"id" => LB_API, "system_name" => "LB_API", "system_code" => 'LB',
		// 		'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 		"class_name" => "game_api_keno", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));

		// 	$this->db->insert($this->game_table, array('gameId' => LB_API, 'game' => 'LB'));
		// }

		// $this->load->model(array('external_system'));
		// $this->external_system->syncCurrentExternalSystem();
	}

	public function down() {
		// $this->db->delete($this->esl_table, array('id' => LB_API));
		// $this->db->delete($this->es_table, array('id' => LB_API));
		// $this->db->delete($this->game_table, array('gameId' => LB_API));
	}
}
