<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_flashpay_to_external_system_list_201701071236 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => FLASHPAY_PAYMENT_API,
		// 	"system_name" => "FLASHPAY_PAYMENT_API",
		// 	"system_code" => "flashpay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_flashpay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://ttflashpay.com/interface/AutoBank/index.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"flashpay_partner\" : \"## Partner ID ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"962\" : \"_json:{\\\"1\\\": \\\"CTTIC\\\", \\\"2\\\": \\\"\u4E2D\u4FE1\u94F6\u884C\\\"}\",\r\n\t\t\"963\" : \"_json:{\\\"1\\\": \\\"BOC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u94F6\u884C\\\"}\",\r\n\t\t\"964\" : \"_json:{\\\"1\\\": \\\"ABC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u519C\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"965\" : \"_json:{\\\"1\\\": \\\"CCB\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\\\"}\",\r\n\t\t\"967\" : \"_json:{\\\"1\\\": \\\"ICBC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u5DE5\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"970\" : \"_json:{\\\"1\\\": \\\"CMB\\\", \\\"2\\\": \\\"\u62DB\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"971\" : \"_json:{\\\"1\\\": \\\"PSBC\\\", \\\"2\\\": \\\"\u90AE\u653F\u50A8\u84C4\\\"}\",\r\n\t\t\"972\" : \"_json:{\\\"1\\\": \\\"CIB\\\", \\\"2\\\": \\\"\u5174\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"976\" : \"_json:{\\\"1\\\": \\\"SRCB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u519C\u6751\u5546\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"977\" : \"_json:{\\\"1\\\": \\\"SPDB\\\", \\\"2\\\": \\\"\u6D66\u4E1C\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"978\" : \"_json:{\\\"1\\\": \\\"PAB\\\", \\\"2\\\": \\\"\u5E73\u5B89\u94F6\u884C\\\"}\",\r\n\t\t\"979\" : \"_json:{\\\"1\\\": \\\"NJCB\\\", \\\"2\\\": \\\"\u5357\u4EAC\u94F6\u884C\\\"}\",\r\n\t\t\"980\" : \"_json:{\\\"1\\\": \\\"CMBC\\\", \\\"2\\\": \\\"\u6C11\u751F\u94F6\u884C\\\"}\",\r\n\t\t\"981\" : \"_json:{\\\"1\\\": \\\"BOCO\\\", \\\"2\\\": \\\"\u4EA4\u901A\u94F6\u884C\\\"}\",\r\n\t\t\"983\" : \"_json:{\\\"1\\\": \\\"HCCB\\\", \\\"2\\\": \\\"\u676D\u5DDE\u94F6\u884C\\\"}\",\r\n\t\t\"985\" : \"_json:{\\\"1\\\": \\\"GDB\\\", \\\"2\\\": \\\"\u5E7F\u4E1C\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"986\" : \"_json:{\\\"1\\\": \\\"CEB\\\", \\\"2\\\": \\\"\u5149\u5927\u94F6\u884C\\\"}\",\r\n\t\t\"987\" : \"_json:{\\\"1\\\": \\\"BEA\\\", \\\"2\\\": \\\"\u4E1C\u4E9A\u94F6\u884C\\\"}\",\r\n\t\t\"989\" : \"_json:{\\\"1\\\": \\\"BCCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u94F6\u884C\\\"}\"\r\n\t}\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => FLASHPAY_ALIPAY_PAYMENT_API,
		// 	"system_name" => "FLASHPAY_ALIPAY_PAYMENT_API",
		// 	"system_code" => "flashpay_alipay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_flashpay_alipay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://ttflashpay.com/interface/AutoBank/index.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"flashpay_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => FLASHPAY_WEIXIN_PAYMENT_API,
		// 	"system_name" => "FLASHPAY_WEIXIN_PAYMENT_API",
		// 	"system_code" => "flashpay_weixin",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_flashpay_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://ttflashpay.com/interface/AutoBank/index.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"flashpay_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => FLASHPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => FLASHPAY_ALIPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => FLASHPAY_WEIXIN_PAYMENT_API));
	}
}
