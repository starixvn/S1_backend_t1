<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_yinbao_to_external_system_list_201610110800 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => YINBAO_PAYMENT_API,
		// 	"system_name" => "YINBAO_PAYMENT_API",
		// 	"system_code" => "YINBAO",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"live_url" => "http://wytj.9vpay.com/PayBank.aspx",
		// 	"sandbox_url" => "http://wytj.9vpay.com/PayBank.aspx",
		// 	"live_key" => "#secret key#",
		// 	"sandbox_key" => "#secret key#",
		// 	"class_name" => "payment_api_yinbao",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\r\n \t\"yinbao_partner\" : \"##Partner ID##\"\r\n }",
		// 	"sandbox_extra_info" => "{\r\n \t\"yinbao_partner\" : \"##Partner ID##\"\r\n }",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => YINBAO_PAYMENT_API));
	}
}
