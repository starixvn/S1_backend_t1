<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_opusgame_to_external_system_list_201603100501 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => OPUS_API, "system_name" => "OPUS_API", "system_code" => 'OPUS_API',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_onesgame", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => OPUS_API));
	}
}
