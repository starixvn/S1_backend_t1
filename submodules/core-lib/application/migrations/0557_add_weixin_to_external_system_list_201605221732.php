<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_weixin_to_external_system_list_201605221732 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => WEIXIN_PAYMENT_API,
		// 	"system_name" => "WEIXIN_PAYMENT_API",
		// 	"system_code" => "WEIXIN",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	# credentials here is the SDK trial account
		// 	# only key, app_id, mch_id are necessary for payment
		// 	"live_key" => "e10adc3949ba59abbe56e057f20f883e",
		// 	"sandbox_key" => "e10adc3949ba59abbe56e057f20f883e",
		// 	"extra_info" =>"{\r\n    \"weixin_app_id\": \"wx426b3015555a46be\",\r\n    \"weixin_mch_id\": \"1225312702\",\r\n    \"weixin_order_expire\": \"600\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n    \"weixin_app_id\": \"wx426b3015555a46be\",\r\n    \"weixin_mch_id\": \"1225312702\",\r\n    \"weixin_order_expire\": \"600\"\r\n}",
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => WEIXIN_PAYMENT_API));
	}
}
