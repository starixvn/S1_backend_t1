<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_daddypay_to_external_system_list_201701130200 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => DADDYPAY_BANKCARD_PAYMENT_API,
		// 	"system_name" => "DADDYPAY_BANKCARD_PAYMENT_API",
		// 	"system_code" => "daddypay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_daddypay_bankcard",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "",
		// 	"sandbox_url" => "http://52.69.65.224/Mownecum_2_API_Live/Deposit?format=json",
		// 	"extra_info" =>"{}",
		// 	"sandbox_extra_info" => "{\r\n\t\"daddypay_company_id\" : \"## company id ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
		// $this->db->insert($this->tableName, array(
		// 	"id" => DADDYPAY_3RDPARTY_PAYMENT_API,
		// 	"system_name" => "DADDYPAY_3RDPARTY_PAYMENT_API",
		// 	"system_code" => "daddypay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_daddypay_3rdparty",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "",
		// 	"sandbox_url" => "http://52.69.65.224/Mownecum_2_API_Live/Deposit?format=json",
		// 	"extra_info" =>"{}",
		// 	"sandbox_extra_info" => "{\r\n\t\"daddypay_company_id\" : \"## company id ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
		// $this->db->insert($this->tableName, array(
		// 	"id" => DADDYPAY_QRCODE_PAYMENT_API,
		// 	"system_name" => "DADDYPAY_QRCODE_PAYMENT_API",
		// 	"system_code" => "daddypay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_daddypay_qrcode",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "",
		// 	"sandbox_url" => "http://52.69.65.224/Mownecum_2_API_Live/Deposit?format=json",
		// 	"extra_info" =>"{}",
		// 	"sandbox_extra_info" => "{\r\n    \"daddypay_company_id\" : \"## company id ##\",\r\n    \"daddypay_alipay_note\" : \"## alipay note: see documentation ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => DADDYPAY_BANKCARD_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => DADDYPAY_3RDPARTY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => DADDYPAY_QRCODE_PAYMENT_API));
	}
}
