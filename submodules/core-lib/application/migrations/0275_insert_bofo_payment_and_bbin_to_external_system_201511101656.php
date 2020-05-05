<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_bofo_payment_and_bbin_to_external_system_201511101656 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//bofo
		// $this->db->insert($this->tableName, array(
		// 	"id" => BOFO_PAYMENT_API, "system_name" => "BOFO_PAYMENT_API", "system_code" => 'BOFO',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0,
		// 	"class_name" => "payment_api_bofo", 'local_path' => 'payment', 'manager' => 'payment_manager'));

		// //bbin
		// $this->db->insert($this->tableName, array(
		// 	"id" => BBIN_API, "system_name" => "BBIN_API", "system_code" => 'BBIN',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_bbin", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));

		// $this->load->model(array('external_system'));
		// $this->external_system->syncCurrentExternalSystem();
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => BOFO_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => BBIN_API));

		// $this->load->model(array('external_system'));
		// $this->external_system->syncCurrentExternalSystem();
	}
}
