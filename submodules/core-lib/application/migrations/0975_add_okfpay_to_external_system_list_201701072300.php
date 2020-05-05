<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_okfpay_to_external_system_list_201701072300 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => OKFPAY_PAYMENT_API,
		// 	"system_name" => "OKFPAY_PAYMENT_API",
		// 	"system_code" => "okfpay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_okfpay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://gateway.okfpay.com/Gate/payindex.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"okfpay_partner\" : \"## Partner ID ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"ICBC\" : \"_json: { \\\"1\\\": \\\"ICBC\\\", \\\"2\\\": \\\"\u5DE5\u5546\u94F6\u884C\\\" }\",\r\n\t\t\"CMB\" : \"_json: { \\\"1\\\": \\\"CMB\\\", \\\"2\\\": \\\"\u62DB\u5546\u94F6\u884C\\\" }\",\r\n\t\t\"CCB\" : \"_json: { \\\"1\\\": \\\"CCB\\\", \\\"2\\\": \\\"\u5EFA\u8BBE\u94F6\u884C\\\" }\",\r\n\t\t\"BOC\" : \"_json: { \\\"1\\\": \\\"BOC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u94F6\u884C\\\" }\",\r\n\t\t\"ABC\" : \"_json: { \\\"1\\\": \\\"ABC\\\", \\\"2\\\": \\\"\u519C\u4E1A\u94F6\u884C\\\" }\",\r\n\t\t\"BOCM\" : \"_json: { \\\"1\\\": \\\"BOCM\\\", \\\"2\\\": \\\"\u4EA4\u901A\u94F6\u884C\\\" }\",\r\n\t\t\"SPDB\" : \"_json: { \\\"1\\\": \\\"SPDB\\\", \\\"2\\\": \\\"\u6D66\u53D1\u94F6\u884C\\\" }\",\r\n\t\t\"CGB\" : \"_json: { \\\"1\\\": \\\"CGB\\\", \\\"2\\\": \\\"\u5E7F\u53D1\u94F6\u884C\\\" }\",\r\n\t\t\"CTITC\" : \"_json: { \\\"1\\\": \\\"CTITC\\\", \\\"2\\\": \\\"\u4E2D\u4FE1\u94F6\u884C\\\" }\",\r\n\t\t\"CEB\" : \"_json: { \\\"1\\\": \\\"CEB\\\", \\\"2\\\": \\\"\u5149\u5927\u94F6\u884C\\\" }\",\r\n\t\t\"CIB\" : \"_json: { \\\"1\\\": \\\"CIB\\\", \\\"2\\\": \\\"\u5174\u4E1A\u94F6\u884C\\\" }\",\r\n\t\t\"SDB\" : \"_json: { \\\"1\\\": \\\"SDB\\\", \\\"2\\\": \\\"\u5E73\u5B89\u94F6\u884C\\\" }\",\r\n\t\t\"CMBC\" : \"_json: { \\\"1\\\": \\\"CMBC\\\", \\\"2\\\": \\\"\u6C11\u751F\u94F6\u884C\\\" }\",\r\n\t\t\"HXB\" : \"_json: { \\\"1\\\": \\\"HXB\\\", \\\"2\\\": \\\"\u534E\u590F\u94F6\u884C\\\" }\",\r\n\t\t\"PSBC\" : \"_json: { \\\"1\\\": \\\"PSBC\\\", \\\"2\\\": \\\"\u90AE\u50A8\u94F6\u884C\\\" }\",\r\n\t\t\"BCCB\" : \"_json: { \\\"1\\\": \\\"BCCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u94F6\u884C\\\" }\",\r\n\t\t\"SHBANK\" : \"_json: { \\\"1\\\": \\\"SHBANK\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u94F6\u884C\\\" }\",\r\n\t\t\"BOHAI\" : \"_json: { \\\"1\\\": \\\"BOHAI\\\", \\\"2\\\": \\\"\u6E24\u6D77\u94F6\u884C\\\" }\",\r\n\t\t\"SHNS\" : \"_json: { \\\"1\\\": \\\"SHNS\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u519C\u5546\\\" }\",\r\n\t\t\"UNION\" : \"_json: { \\\"1\\\": \\\"UNION\\\", \\\"2\\\": \\\"\u94F6\u8054\u652F\u4ED8\\\" }\"\r\n\t}\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => OKFPAY_ALIPAY_PAYMENT_API,
		// 	"system_name" => "OKFPAY_ALIPAY_PAYMENT_API",
		// 	"system_code" => "okfpay_alipay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_okfpay_alipay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://gateway.okfpay.com/Gate/payindex.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"okfpay_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));

		// $this->db->insert($this->tableName, array(
		// 	"id" => OKFPAY_WEIXIN_PAYMENT_API,
		// 	"system_name" => "OKFPAY_WEIXIN_PAYMENT_API",
		// 	"system_code" => "okfpay_weixin",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_okfpay_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## sign key ##",
		// 	"sandbox_key" => "",
		// 	"live_url" => "https://gateway.okfpay.com/Gate/payindex.aspx",
		// 	"sandbox_url" => "",
		// 	"extra_info" =>"{\r\n\t\"okfpay_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => OKFPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => OKFPAY_ALIPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => OKFPAY_WEIXIN_PAYMENT_API));
	}
}
