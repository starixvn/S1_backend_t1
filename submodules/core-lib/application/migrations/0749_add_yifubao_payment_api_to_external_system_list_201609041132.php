<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_yifubao_payment_api_to_external_system_list_201609041132 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => YIFUBAO_PAYMENT_API,
		// 	"system_name" => "YIFUBAO_PAYMENT_API",
		// 	"system_code" => "yifubao",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_yifubao",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://pay.41.cn/gateway",
		// 	"sandbox_url" => "https://pay.41.cn/gateway",
		// 	"extra_info" =>"{\r\n \t\"yifubao_merchant_code\" : \"<merchant code>\"\r\n }",
		// 	"sandbox_extra_info" => "{\r\n \t\"yifubao_merchant_code\" : \"<merchant code>\"\r\n }",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => YIFUBAO_PAYMENT_API));
	}
}
