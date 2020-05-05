<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_funpay_to_external_system_list_201611080200 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => FUNPAY_PAYMENT_API,
		// 	"system_name" => "FUNPAY_PAYMENT_API",
		// 	"system_code" => "funpay",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_funpay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## MD5 Key ##",
		// 	"sandbox_key" => "## MD5 Key ##",
		// 	"live_url" => "https://www.funpay.com/website/BatchPay.htm",
		// 	"sandbox_url" => "https://www.funpay.com/website/BatchPay.htm",
		// 	"extra_info" =>"{\r\n\t\"funpay_merchant_code\": \"## Merchant Code ##\",\r\n\t\"funpay_bankInfo\" : [\r\n\t\t[1, \"\u4E2D\u56FD\u5DE5\u5546\u94F6\u884C\"],\r\n\t\t[4, \"\u4E2D\u56FD\u519C\u4E1A\u94F6\u884C\"],\r\n\t\t[6, \"\u4E2D\u56FD\u94F6\u884C\"],\r\n\t\t[3, \"\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\"],\r\n\t\t[5, \"\u4EA4\u901A\u94F6\u884C\"],\r\n\t\t[10, \"\u4E2D\u4FE1\u94F6\u884C\"],\r\n\t\t[20, \"\u4E2D\u56FD\u5149\u5927\u94F6\u884C\"],\r\n\t\t[14, \"\u534E\u590F\u94F6\u884C\"],\r\n\t\t[11, \"\u4E2D\u56FD\u6C11\u751F\u94F6\u884C\"],\r\n\t\t[15, \"\u5E73\u5B89\u94F6\u884C\"],\r\n\t\t[8, \"\u5E7F\u4E1C\u53D1\u5C55\u94F6\u884C\"],\r\n\t\t[2, \"\u62DB\u5546\u94F6\u884C\"],\r\n\t\t[13, \"\u5174\u4E1A\u94F6\u884C\"],\r\n\t\t[24, \"\u4E0A\u6D77\u6D66\u4E1C\u53D1\u5C55\u94F6\u884C\"],\r\n\t\t[17, \"\u5E7F\u5DDE\u94F6\u884C\"],\r\n\t\t[12, \"\u4E2D\u56FD\u90AE\u653F\u50A8\u84C4\u94F6\u884C\"]\r\n\t]\r\n}\r\n",
		// 	"sandbox_extra_info" => "{\r\n\t\"funpay_merchant_code\": \"## Merchant Code ##\",\r\n\t\"funpay_bankInfo\" : [\r\n\t\t[1, \"\u4E2D\u56FD\u5DE5\u5546\u94F6\u884C\"],\r\n\t\t[4, \"\u4E2D\u56FD\u519C\u4E1A\u94F6\u884C\"],\r\n\t\t[6, \"\u4E2D\u56FD\u94F6\u884C\"],\r\n\t\t[3, \"\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\"],\r\n\t\t[5, \"\u4EA4\u901A\u94F6\u884C\"],\r\n\t\t[10, \"\u4E2D\u4FE1\u94F6\u884C\"],\r\n\t\t[20, \"\u4E2D\u56FD\u5149\u5927\u94F6\u884C\"],\r\n\t\t[14, \"\u534E\u590F\u94F6\u884C\"],\r\n\t\t[11, \"\u4E2D\u56FD\u6C11\u751F\u94F6\u884C\"],\r\n\t\t[15, \"\u5E73\u5B89\u94F6\u884C\"],\r\n\t\t[8, \"\u5E7F\u4E1C\u53D1\u5C55\u94F6\u884C\"],\r\n\t\t[2, \"\u62DB\u5546\u94F6\u884C\"],\r\n\t\t[13, \"\u5174\u4E1A\u94F6\u884C\"],\r\n\t\t[24, \"\u4E0A\u6D77\u6D66\u4E1C\u53D1\u5C55\u94F6\u884C\"],\r\n\t\t[17, \"\u5E7F\u5DDE\u94F6\u884C\"],\r\n\t\t[12, \"\u4E2D\u56FD\u90AE\u653F\u50A8\u84C4\u94F6\u884C\"]\r\n\t]\r\n}\r\n",
		// 	"allow_deposit_withdraw" => 2,
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => FUNPAY_PAYMENT_API));
	}
}
