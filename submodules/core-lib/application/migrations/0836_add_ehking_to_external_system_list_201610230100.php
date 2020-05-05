<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ehking_to_external_system_list_201610230100 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => EHKING_PAYMENT_API,
		// 	"system_name" => "EHKING_PAYMENT_API",
		// 	"system_code" => "EHKING",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"live_url" => "https://api.ehking.com/onlinePay/order",
		// 	"sandbox_url" => "https://api.ehking.com/onlinePay/order",
		// 	"live_key" => "#secret key#",
		// 	"sandbox_key" => "#secret key#",
		// 	"class_name" => "payment_api_ehking",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\r\n \t\"ehking_merchantId\" : \"##Merchant ID##\"\r\n }",
		// 	"sandbox_extra_info" => "{\r\n \t\"ehking_merchantId\" : \"##Merchant ID##\"\r\n }",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => EHKING_PAYMENT_API));
	}
}
