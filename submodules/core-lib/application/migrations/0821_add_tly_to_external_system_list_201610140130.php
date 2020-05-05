<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tly_to_external_system_list_201610140130 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => TLY_PAYMENT_API,
		// 	"system_name" => "TLY_PAYMENT_API",
		// 	"system_code" => "TLY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"live_url" => "https://s01.tonglueyun.com/authority/system/api/place_order/",
		// 	"sandbox_url" => "https://s01.tonglueyun.com/authority/system/api/place_order/",
		// 	"live_key" => "#API Key#",
		// 	"sandbox_key" => "#API Key#",
		// 	"class_name" => "payment_api_tly",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\n\t\"bank_info\" : {\n\t\t\"##bank id##\" : { \"card_number\" : \"##card number##\", \"name\" : \"##card name##\" }\n\t},\n\t\"bank_list\" : {\n\t\t\"ABC\" : \"_json: { \\\"1\\\" : \\\"ABC\\\", \\\"2\\\" : \\\"农行\\\" }\",\n\t\t\"BOC\" : \"_json: { \\\"1\\\" : \\\"BOC\\\", \\\"2\\\" : \\\"中行\\\" }\",\n\t\t\"ICBC\" : \"_json: { \\\"1\\\" : \\\"ICBC\\\", \\\"2\\\" : \\\"工行\\\" }\",\n\t\t\"BCM\" : \"_json: { \\\"1\\\" : \\\"BCM\\\", \\\"2\\\" : \\\"交行\\\" }\",\n\t\t\"CCB\" : \"_json: { \\\"1\\\" : \\\"CCB\\\", \\\"2\\\" : \\\"建行\\\" }\",\n\t\t\"CMB\" : \"_json: { \\\"1\\\" : \\\"CMB\\\", \\\"2\\\" : \\\"招行\\\" }\",\n\t\t\"CMBC\" : \"_json: { \\\"1\\\" : \\\"CMBC\\\", \\\"2\\\" : \\\"民生\\\" }\",\n\t\t\"HXB\" : \"_json: { \\\"1\\\" : \\\"HXB\\\", \\\"2\\\" : \\\"华夏\\\" }\",\n\t\t\"PSBC\" : \"_json: { \\\"1\\\" : \\\"PSBC\\\", \\\"2\\\" : \\\"邮政\\\" }\",\n\t\t\"WebMM\" : \"_json: { \\\"1\\\" : \\\"WebMM\\\", \\\"2\\\" : \\\"微信\\\" }\",\n\t\t\"ALIPAY\" : \"_json: { \\\"1\\\" : \\\"ALIPAY\\\", \\\"2\\\" : \\\"支付宝\\\" }\"\n\t}\n}",
		// 	"sandbox_extra_info" => "{\n\t\"bank_info\" : {\n\t\t\"##bank id##\" : { \"card_number\" : \"##card number##\", \"name\" : \"##card name##\" }\n\t},\n\t\"bank_list\" : {\n\t\t\"ABC\" : \"_json: { \\\"1\\\" : \\\"ABC\\\", \\\"2\\\" : \\\"农行\\\" }\",\n\t\t\"BOC\" : \"_json: { \\\"1\\\" : \\\"BOC\\\", \\\"2\\\" : \\\"中行\\\" }\",\n\t\t\"ICBC\" : \"_json: { \\\"1\\\" : \\\"ICBC\\\", \\\"2\\\" : \\\"工行\\\" }\",\n\t\t\"BCM\" : \"_json: { \\\"1\\\" : \\\"BCM\\\", \\\"2\\\" : \\\"交行\\\" }\",\n\t\t\"CCB\" : \"_json: { \\\"1\\\" : \\\"CCB\\\", \\\"2\\\" : \\\"建行\\\" }\",\n\t\t\"CMB\" : \"_json: { \\\"1\\\" : \\\"CMB\\\", \\\"2\\\" : \\\"招行\\\" }\",\n\t\t\"CMBC\" : \"_json: { \\\"1\\\" : \\\"CMBC\\\", \\\"2\\\" : \\\"民生\\\" }\",\n\t\t\"HXB\" : \"_json: { \\\"1\\\" : \\\"HXB\\\", \\\"2\\\" : \\\"华夏\\\" }\",\n\t\t\"PSBC\" : \"_json: { \\\"1\\\" : \\\"PSBC\\\", \\\"2\\\" : \\\"邮政\\\" }\",\n\t\t\"WebMM\" : \"_json: { \\\"1\\\" : \\\"WebMM\\\", \\\"2\\\" : \\\"微信\\\" }\",\n\t\t\"ALIPAY\" : \"_json: { \\\"1\\\" : \\\"ALIPAY\\\", \\\"2\\\" : \\\"支付宝\\\" }\"\n\t}\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => TLY_PAYMENT_API));
	}
}
