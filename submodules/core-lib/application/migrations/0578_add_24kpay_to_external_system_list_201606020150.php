<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_24kpay_to_external_system_list_201606020150 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => PAY24K_PAYMENT_API,
		// 	"system_name" => "PAY24K_PAYMENT_API",
		// 	"system_code" => "24KPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_24kpay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "<enter key here>",
		// 	"sandbox_key" => "<enter key here>",
		// 	"live_url" => "http://api.24kpay.com/",
		// 	"sandbox_url" => "http://test.24kpay.com/mapi/",
		// 	"extra_info" =>"{\r\n    \"24kpay_merchantId\": \"xxx\",\r\n    \"24kpay_inAcctNum\": \"123\",\r\n    \"24kpay_inAcctName\": \"\",\r\n    \"24kpay_inBankName\": \"\",\r\n    \"24kpay_inBankCode\": \"\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n    \"24kpay_merchantId\": \"xxx\",\r\n    \"24kpay_inAcctNum\": \"123\",\r\n    \"24kpay_inAcctName\": \"\",\r\n    \"24kpay_inBankName\": \"\",\r\n    \"24kpay_inBankCode\": \"\"\r\n}",
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => PAY24K_PAYMENT_API));
	}
}
