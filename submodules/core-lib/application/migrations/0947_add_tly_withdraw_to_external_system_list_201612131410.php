<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tly_withdraw_to_external_system_list_201612131410 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => TLY_WITHDRAW_PAYMENT_API,
		// 	"system_name" => "TLY_WITHDRAW_PAYMENT_API",
		// 	"system_code" => "tly_withdraw",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_tly_withdraw",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "<enter apiKey here>",
		// 	"live_url" => "https://www.tly-transfer.com/sfisapi/",
		// 	"extra_info" =>"{\r\n\t\"tly_company_name\" : \"## TLY Company Name ##\",\r\n\t\"tly_bank_info\" : [\r\n\t\t[1, \"\u4E2D\u56FD\u5DE5\u5546\u94F6\u884C\", \"ICBC\"], [4, \"\u4E2D\u56FD\u519C\u4E1A\u94F6\u884C\", \"ABC\"], [6, \"\u4E2D\u56FD\u94F6\u884C\", \"BOC\"], [3, \"\u4E2D\u56FD\u5EFA\u8BBE\u94F6\u884C\", \"CCB\"], [5, \"\u4E2D\u56FD\u4EA4\u901A\u94F6\u884C\", \"BCM\"], [10, \"\u4E2D\u56FD\u4E2D\u4FE1\u94F6\u884C\", \"CNCB\"], [20, \"\u4E2D\u56FD\u5149\u5927\u94F6\u884C\", \"CEB\"], [14, \"\u4E2D\u56FD\u534E\u590F\u94F6\u884C\", \"HXB\"], [11, \"\u4E2D\u56FD\u6C11\u751F\u94F6\u884C\", \"CMBC\"], [15, \"\u4E2D\u56FD\u5E73\u5B89\u94F6\u884C\", \"PAB\"], [8, \"\u5E7F\u4E1C\u53D1\u5C55\u94F6\u884C\", \"GDB\"], [2, \"\u4E2D\u56FD\u62DB\u5546\u94F6\u884C\", \"CMB\"], [13, \"\u4E2D\u56FD\u5174\u4E1A\u94F6\u884C\", \"CIB\"], [18, \"\u5357\u4EAC\u94F6\u884C\", \"NJCB\"], [17, \"\u5E7F\u5DDE\u94F6\u884C\", \"GZCB\"], [12, \"\u4E2D\u56FD\u90AE\u653F\u50A8\u84C4\u94F6\u884C\", \"PSBC\"]\r\n\t]\r\n}",
		// 	"allow_deposit_withdraw" => "2"
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => TLY_WITHDRAW_PAYMENT_API));
	}
}
