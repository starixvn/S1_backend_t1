<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sdpay_to_external_system_list_201607210040 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => SDPAY_PAYMENT_API,
		// 	"system_name" => "SDPAY_PAYMENT_API",
		// 	"system_code" => "SDPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_sdpay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://deposit2.sdapayapi.com/9001/ApplyForABank.asmx?wsdl",
		// 	"sandbox_url" => "https://deposit2.sdapayapi.com/9001/ApplyForABank.asmx?wsdl",
		// 	"extra_info" =>"{\r\n \t\"sdpay_LoginAccount\" : \"<merchant code>\",\r\n\t\"sdpay_key1\" : \"\",\r\n\t\"sdpay_key2\" : \"\"\r\n }",
		// 	"sandbox_extra_info" => "{\r\n \t\"sdpay_LoginAccount\" : \"<merchant code>\",\r\n\t\"sdpay_key1\" : \"\",\r\n\t\"sdpay_key2\" : \"\"\r\n }",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => SDPAY_PAYMENT_API));
	}
}
