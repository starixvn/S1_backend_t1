<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_kenogame_to_external_system_list_201603291913 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => KENOGAME_API, "system_name" => "KENOGAME_API", "system_code" => 'KENOGAME',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_kenogame", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => KENOGAME_API));
	}
}
