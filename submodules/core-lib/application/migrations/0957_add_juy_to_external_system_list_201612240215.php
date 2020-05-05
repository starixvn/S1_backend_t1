<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_juy_to_external_system_list_201612240215 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => JUY_PAYMENT_API,
		// 	"system_name" => "JUY_PAYMENT_API",
		// 	"system_code" => "JUY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"live_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"sandbox_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"live_key" => "## API KEY ##",
		// 	"sandbox_key" => "## API KEY ##",
		// 	"class_name" => "payment_api_juy",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"ICBC\" : \"_json:{\\\"1\\\": \\\"ICBC\\\", \\\"2\\\": \\\"\u5DE5\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"ABC\" : \"_json:{\\\"1\\\": \\\"ABC\\\", \\\"2\\\": \\\"\u519C\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"CCB\" : \"_json:{\\\"1\\\": \\\"CCB\\\", \\\"2\\\": \\\"\u5EFA\u8BBE\u94F6\u884C\\\"}\",\r\n\t\t\"BOC\" : \"_json:{\\\"1\\\": \\\"BOC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u94F6\u884C\\\"}\",\r\n\t\t\"CMB\" : \"_json:{\\\"1\\\": \\\"CMB\\\", \\\"2\\\": \\\"\u62DB\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"BCCB\" : \"_json:{\\\"1\\\": \\\"BCCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u94F6\u884C\\\"}\",\r\n\t\t\"BOCO\" : \"_json:{\\\"1\\\": \\\"BOCO\\\", \\\"2\\\": \\\"\u4EA4\u901A\u94F6\u884C\\\"}\",\r\n\t\t\"CIB\" : \"_json:{\\\"1\\\": \\\"CIB\\\", \\\"2\\\": \\\"\u5174\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"NJCB\" : \"_json:{\\\"1\\\": \\\"NJCB\\\", \\\"2\\\": \\\"\u5357\u4EAC\u94F6\u884C\\\"}\",\r\n\t\t\"CMBC\" : \"_json:{\\\"1\\\": \\\"CMBC\\\", \\\"2\\\": \\\"\u6C11\u751F\u94F6\u884C\\\"}\",\r\n\t\t\"CEB\" : \"_json:{\\\"1\\\": \\\"CEB\\\", \\\"2\\\": \\\"\u5149\u5927\u94F6\u884C\\\"}\",\r\n\t\t\"PINGANBANK\" : \"_json:{\\\"1\\\": \\\"PINGANBANK\\\", \\\"2\\\": \\\"\u5E73\u5B89\u94F6\u884C\\\"}\",\r\n\t\t\"CBHB\" : \"_json:{\\\"1\\\": \\\"CBHB\\\", \\\"2\\\": \\\"\u6E24\u6D77\u94F6\u884C\\\"}\",\r\n\t\t\"HKBEA\" : \"_json:{\\\"1\\\": \\\"HKBEA\\\", \\\"2\\\": \\\"\u4E1C\u4E9A\u94F6\u884C\\\"}\",\r\n\t\t\"NBCB\" : \"_json:{\\\"1\\\": \\\"NBCB\\\", \\\"2\\\": \\\"\u5B81\u6CE2\u94F6\u884C\\\"}\",\r\n\t\t\"CTTIC\" : \"_json:{\\\"1\\\": \\\"CTTIC\\\", \\\"2\\\": \\\"\u4E2D\u4FE1\u94F6\u884C\\\"}\",\r\n\t\t\"GDB\" : \"_json:{\\\"1\\\": \\\"GDB\\\", \\\"2\\\": \\\"\u5E7F\u53D1\u94F6\u884C\\\"}\",\r\n\t\t\"SHB\" : \"_json:{\\\"1\\\": \\\"SHB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u94F6\u884C\\\"}\",\r\n\t\t\"SPDB\" : \"_json:{\\\"1\\\": \\\"SPDB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u6D66\u4E1C\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"PSBS\" : \"_json:{\\\"1\\\": \\\"PSBS\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u90AE\u653F\\\"}\",\r\n\t\t\"HXB\" : \"_json:{\\\"1\\\": \\\"HXB\\\", \\\"2\\\": \\\"\u534E\u590F\u94F6\u884C\\\"}\",\r\n\t\t\"BJRCB\" : \"_json:{\\\"1\\\": \\\"BJRCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u519C\u6751\u5546\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"SRCB\" : \"_json:{\\\"1\\\": \\\"SRCB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u519C\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"SDB\" : \"_json:{\\\"1\\\": \\\"SDB\\\", \\\"2\\\": \\\"\u6DF1\u5733\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"CZB\" : \"_json:{\\\"1\\\": \\\"CZB\\\", \\\"2\\\": \\\"\u6D59\u6C5F\u7A20\u5DDE\u5546\u4E1A\u94F6\u884C\\\"}\"\r\n\t}\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"ICBC\" : \"_json:{\\\"1\\\": \\\"ICBC\\\", \\\"2\\\": \\\"\u5DE5\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"ABC\" : \"_json:{\\\"1\\\": \\\"ABC\\\", \\\"2\\\": \\\"\u519C\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"CCB\" : \"_json:{\\\"1\\\": \\\"CCB\\\", \\\"2\\\": \\\"\u5EFA\u8BBE\u94F6\u884C\\\"}\",\r\n\t\t\"BOC\" : \"_json:{\\\"1\\\": \\\"BOC\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u94F6\u884C\\\"}\",\r\n\t\t\"CMB\" : \"_json:{\\\"1\\\": \\\"CMB\\\", \\\"2\\\": \\\"\u62DB\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"BCCB\" : \"_json:{\\\"1\\\": \\\"BCCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u94F6\u884C\\\"}\",\r\n\t\t\"BOCO\" : \"_json:{\\\"1\\\": \\\"BOCO\\\", \\\"2\\\": \\\"\u4EA4\u901A\u94F6\u884C\\\"}\",\r\n\t\t\"CIB\" : \"_json:{\\\"1\\\": \\\"CIB\\\", \\\"2\\\": \\\"\u5174\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"NJCB\" : \"_json:{\\\"1\\\": \\\"NJCB\\\", \\\"2\\\": \\\"\u5357\u4EAC\u94F6\u884C\\\"}\",\r\n\t\t\"CMBC\" : \"_json:{\\\"1\\\": \\\"CMBC\\\", \\\"2\\\": \\\"\u6C11\u751F\u94F6\u884C\\\"}\",\r\n\t\t\"CEB\" : \"_json:{\\\"1\\\": \\\"CEB\\\", \\\"2\\\": \\\"\u5149\u5927\u94F6\u884C\\\"}\",\r\n\t\t\"PINGANBANK\" : \"_json:{\\\"1\\\": \\\"PINGANBANK\\\", \\\"2\\\": \\\"\u5E73\u5B89\u94F6\u884C\\\"}\",\r\n\t\t\"CBHB\" : \"_json:{\\\"1\\\": \\\"CBHB\\\", \\\"2\\\": \\\"\u6E24\u6D77\u94F6\u884C\\\"}\",\r\n\t\t\"HKBEA\" : \"_json:{\\\"1\\\": \\\"HKBEA\\\", \\\"2\\\": \\\"\u4E1C\u4E9A\u94F6\u884C\\\"}\",\r\n\t\t\"NBCB\" : \"_json:{\\\"1\\\": \\\"NBCB\\\", \\\"2\\\": \\\"\u5B81\u6CE2\u94F6\u884C\\\"}\",\r\n\t\t\"CTTIC\" : \"_json:{\\\"1\\\": \\\"CTTIC\\\", \\\"2\\\": \\\"\u4E2D\u4FE1\u94F6\u884C\\\"}\",\r\n\t\t\"GDB\" : \"_json:{\\\"1\\\": \\\"GDB\\\", \\\"2\\\": \\\"\u5E7F\u53D1\u94F6\u884C\\\"}\",\r\n\t\t\"SHB\" : \"_json:{\\\"1\\\": \\\"SHB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u94F6\u884C\\\"}\",\r\n\t\t\"SPDB\" : \"_json:{\\\"1\\\": \\\"SPDB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u6D66\u4E1C\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"PSBS\" : \"_json:{\\\"1\\\": \\\"PSBS\\\", \\\"2\\\": \\\"\u4E2D\u56FD\u90AE\u653F\\\"}\",\r\n\t\t\"HXB\" : \"_json:{\\\"1\\\": \\\"HXB\\\", \\\"2\\\": \\\"\u534E\u590F\u94F6\u884C\\\"}\",\r\n\t\t\"BJRCB\" : \"_json:{\\\"1\\\": \\\"BJRCB\\\", \\\"2\\\": \\\"\u5317\u4EAC\u519C\u6751\u5546\u4E1A\u94F6\u884C\\\"}\",\r\n\t\t\"SRCB\" : \"_json:{\\\"1\\\": \\\"SRCB\\\", \\\"2\\\": \\\"\u4E0A\u6D77\u519C\u5546\u94F6\u884C\\\"}\",\r\n\t\t\"SDB\" : \"_json:{\\\"1\\\": \\\"SDB\\\", \\\"2\\\": \\\"\u6DF1\u5733\u53D1\u5C55\u94F6\u884C\\\"}\",\r\n\t\t\"CZB\" : \"_json:{\\\"1\\\": \\\"CZB\\\", \\\"2\\\": \\\"\u6D59\u6C5F\u7A20\u5DDE\u5546\u4E1A\u94F6\u884C\\\"}\"\r\n\t}\r\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));
		// $this->db->insert($this->tableName, array(
		// 	"id" => JUY_ALIPAY_PAYMENT_API,
		// 	"system_name" => "JUY_ALIPAY_PAYMENT_API",
		// 	"system_code" => "JUY_ALIPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"live_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"sandbox_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"live_key" => "## API KEY ##",
		// 	"sandbox_key" => "## API KEY ##",
		// 	"class_name" => "payment_api_juy_alipay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));
		// $this->db->insert($this->tableName, array(
		// 	"id" => JUY_WEIXIN_PAYMENT_API,
		// 	"system_name" => "JUY_WEIXIN_PAYMENT_API",
		// 	"system_code" => "JUY_WEIXIN",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"live_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"sandbox_url" => "http://pay.juypay.com/PayBank.aspx",
		// 	"live_key" => "## API KEY ##",
		// 	"sandbox_key" => "## API KEY ##",
		// 	"class_name" => "payment_api_juy_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"juy_partner\" : \"## Partner ID ##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => JUY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => JUY_ALIPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => JUY_WEIXIN_PAYMENT_API));
	}
}
