<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_inteplay_to_external_system_list_201603042300 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => INTEPLAY_API, "system_name" => "INTEPLAY_API", "system_code" => 'INTEPLAY',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_inteplay", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => INTEPLAY_API));
	}
}
