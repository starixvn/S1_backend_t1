<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_truegateway_to_external_system_list_201610060216 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => TRUEGATEWAY_PAYMENT_API,
		// 	"system_name" => "TRUEGATEWAY_PAYMENT_API",
		// 	"system_code" => "TRUEGATEWAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_truegateway",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "##PassCode##",
		// 	"sandbox_key" => "##PassCode##",
		// 	"live_url" => "https://secure.truegateway.com/transaction/directPayment",
		// 	"sandbox_url" => "https://secure.truegateway.com/transaction/directPayment",
		// 	"extra_info" => "{\r\n\t\"truegateway_merchantID\" : \"##Merchant ID##\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"truegateway_merchantID\" : \"##Merchant ID##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => TRUEGATEWAY_PAYMENT_API));
	}
}
