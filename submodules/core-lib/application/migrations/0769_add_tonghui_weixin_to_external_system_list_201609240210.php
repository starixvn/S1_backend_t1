<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tonghui_weixin_to_external_system_list_201609240210 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => TONGHUI_WEIXIN_PAYMENT_API,
		// 	"system_name" => "TONGHUI_WEIXIN_PAYMENT_API",
		// 	"system_code" => "TONGHUI",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"live_url" => "https://pay.41.cn/gateway",
		// 	"sandbox_url" => "https://pay.41.cn/gateway",
		// 	"class_name" => "payment_api_tonghui_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => '{"tonghui_merchant_code": "" }',
		// 	"sandbox_extra_info" => '{"tonghui_merchant_code": "" }',
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => TONGHUI_WEIXIN_PAYMENT_API));
	}
}
