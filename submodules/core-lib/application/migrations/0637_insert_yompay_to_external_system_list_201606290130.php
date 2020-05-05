<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_yompay_to_external_system_list_201606290130 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => YOMPAY_PAYMENT_API,
		// 	"system_name" => "YOMPAY_PAYMENT_API",
		// 	"system_code" => "YOMPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_yompay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"allow_deposit_withdraw" => 1,

		// 	"live_url" => "https://www.yompay.com/Payapi/",
		// 	"sandbox_url" => "https://www.yompay.com/Payapi/",
		// 	"live_key" => "<MER_KEY>",
		// 	"sandbox_key" => "<MER_KEY>",
		// 	"extra_info" => "{\r\n    \"yompay_MER_NO\" : \"\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n    \"yompay_MER_NO\" : \"\"\r\n}",
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => YOMPAY_PAYMENT_API));
	}
}
