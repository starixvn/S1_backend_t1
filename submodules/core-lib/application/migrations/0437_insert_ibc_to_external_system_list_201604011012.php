<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_ibc_to_external_system_list_201604011012 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => IBC_API, "system_name" => "IBC_API", "system_code" => 'IBC',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_ibc", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => IBC_API));
	}
}
