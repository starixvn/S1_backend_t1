<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_dummy_apis_to_external_system_list_201609030351 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => DUMMY_PAYMENT_API,
		// 	"system_name" => "DUMMY_PAYMENT_API",
		// 	"system_code" => "dummy",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_dummy",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => DUMMY_GAME_API,
		// 	"system_name" => "DUMMY_GAME_API",
		// 	"system_code" => 'dummy',
		// 	'system_type' => SYSTEM_GAME_API,
		// 	"live_mode" => 0,
		// 	"class_name" => "game_api_dummy",
		// 	'local_path' => 'game_platform',
		// 	'manager' => 'game_platform_manager',
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => DUMMY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => DUMMY_GAME_API));
	}
}
