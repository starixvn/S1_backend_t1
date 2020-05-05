<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tonghui_to_external_system_list_201605151105 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => TONGHUI_PAYMENT_API,
		// 	"system_name" => "TONGHUI_PAYMENT_API",
		// 	"system_code" => "TONGHUI",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_tonghui",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => '{"tonghui_merchant_code": "" }',
		// 	"sandbox_extra_info" => '{"tonghui_merchant_code": "" }',
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => TONGHUI_PAYMENT_API));
	}
}
