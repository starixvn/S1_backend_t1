<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_funpay_deposit_in_external_system_list_201611120107 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->delete($this->tableName, array('id' => FUNPAY_DEPOSIT_PAYMENT_API));

		// $this->db->insert($this->tableName, array(
		// 	"id" => FUNPAY_DEPOSIT_PAYMENT_API,
		// 	"system_name" => "FUNPAY_DEPOSIT_PAYMENT_API",
		// 	"system_code" => "funpay_deposit",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_funpay_deposit",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://www.funpay.com/website/pay.htm",
		// 	"sandbox_url" => "https://www.funpay.com/website/pay.htm",
		// 	"extra_info" =>"{\r\n\t\"funpay_merchant_code\": \"## Merchant Code ##\",\r\n\t\"funpay_long_md5key\": \"## MD5 Key ##\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"funpay_merchant_code\": \"## Merchant Code ##\",\r\n\t\"funpay_long_md5key\": \"## MD5 Key ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
	}
}
