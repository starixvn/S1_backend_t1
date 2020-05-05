<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_lamic_to_external_system_list_201612172140 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => LAMIC_PAYMENT_API,
		// 	"system_name" => "LAMIC_PAYMENT_API",
		// 	"system_code" => "lamic",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_lamic",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://www.lamic.cn",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"lamic_uid\": \"## account ##\",\r\n\t\"lamic_pwd\": \"## password ##\",\r\n\t\"lamic_des_key\": \"## des encryption key ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"1\" : \"_json: { \\\"1\\\": \\\"ALIPAY\\\", \\\"2\\\": \\\"\u652F\u4ED8\u5B9D\\\" }\",\r\n\t\t\"2\" : \"_json: { \\\"1\\\": \\\"WXPAY\\\", \\\"2\\\": \\\"\u5FAE\u4FE1\u652F\u4ED8\\\" }\",\r\n\t\t\"9\" : \"_json: { \\\"1\\\": \\\"TENPAY\\\", \\\"2\\\": \\\"QQ\u94B1\u5305\\\" }\"\r\n\t}\r\n}",
		// 	"sandbox_extra_info" => "",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => LAMIC_PAYMENT_API));
	}
}
