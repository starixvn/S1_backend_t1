<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_impt_to_external_system_list_20160416 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => IMPT_API, "system_name" => "IMPT_API", "system_code" => 'BS',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_impt", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
		// $this->db->insert('game', array("gameId" => IMPT_API, "game" => "BS"));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => IMPT_API));
		// $this->db->delete('game', array('gameId' => IMPT_API));
	}
}
