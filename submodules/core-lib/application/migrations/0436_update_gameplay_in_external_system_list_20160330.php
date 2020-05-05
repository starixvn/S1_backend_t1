<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_gameplay_in_external_system_list_20160330 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->delete($this->tableName, array('id' => GAMEPLAY_API));
		// $this->db->insert($this->tableName, array(
		// 	"id" => GAMEPLAY_API, "system_name" => "GAMEPLAY_API", "system_code" => "GAMEPLAY_API",
		// 	"system_type" => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_gameplay", "local_path" => "game_platform", "manager" => "game_platform_manager"));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => GAMEPLAY_API));
	}
}
