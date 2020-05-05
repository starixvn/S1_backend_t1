<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_xinbao_to_external_system_list_201612270200 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => XINBAO_ALIPAY_PAYMENT_API,
		// 	"system_name" => "XINBAO_ALIPAY_PAYMENT_API",
		// 	"system_code" => "xinbao_alipay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_xinbao_alipay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://api.pk767.com/pay",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"xinbao_merchant_code\" : \"## Merchant Code ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => XINBAO_WEIXIN_PAYMENT_API,
		// 	"system_name" => "XINBAO_WEIXIN_PAYMENT_API",
		// 	"system_code" => "xinbao_weixin",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_xinbao_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://api.pk767.com/pay",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"xinbao_merchant_code\" : \"## Merchant Code ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => XINBAO_ALIPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => XINBAO_WEIXIN_PAYMENT_API));
	}
}
