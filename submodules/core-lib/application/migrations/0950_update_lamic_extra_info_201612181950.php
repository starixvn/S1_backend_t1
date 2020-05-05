<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_lamic_extra_info_201612181950 extends CI_Migration {

	public function up() {

		// $this->db->where('id', LAMIC_PAYMENT_API)
		// 		 ->update('external_system_list', array(
		// 		"extra_info" =>"{\r\n\t\"lamic_uid\": \"## account ##\",\r\n\t\"lamic_pwd\": \"## password ##\",\r\n\t\"lamic_des_key\": \"## des encryption key ##\",\r\n\t\"bank_list\" : {\r\n\t\t\"alipay\" : \"_json: { \\\"1\\\": \\\"ALIPAY\\\", \\\"2\\\": \\\"\u652F\u4ED8\u5B9D\\\" }\",\r\n\t\t\"wxpay\" : \"_json: { \\\"1\\\": \\\"WXPAY\\\", \\\"2\\\": \\\"\u5FAE\u4FE1\u652F\u4ED8\\\" }\",\r\n\t\t\"tenpay\" : \"_json: { \\\"1\\\": \\\"TENPAY\\\", \\\"2\\\": \\\"QQ\u94B1\u5305\\\" }\"\r\n\t}\r\n}",
		// 	));
	}

	public function down() {
	}
}
