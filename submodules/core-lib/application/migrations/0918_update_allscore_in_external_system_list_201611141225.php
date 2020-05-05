<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_allscore_in_external_system_list_201611141225 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->delete($this->tableName, array('id' => ALLSCORE_PAYMENT_API));

		// $this->db->insert($this->tableName, array(
		// 	"id" => ALLSCORE_PAYMENT_API,
		// 	"system_name" => "ALLSCORE_PAYMENT_API",
		// 	"system_code" => "allscore",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"class_name" => "payment_api_allscore",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "## MD5 key ##",
		// 	"sandbox_key" => "## MD5 key ##",
		// 	"live_url" => "https://paymenta.allscore.com/olgateway/serviceDirect.htm",
		// 	"sandbox_url" => "http://119.61.12.89:8090/olgateway/serviceDirect.htm",
		// 	"extra_info" =>"{\r\n     \"allscore_merchantId\": \"## Merchant ID ##\",\r\n     \"allscore_signType\" : \"MD5/RSA\",\r\n     \"allscore_priv_key\" : \"\",\r\n     \"allscore_pub_key\" : \"\",\r\n     \"bank_list\" : \"\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n     \"allscore_merchantId\": \"## Merchant ID ##\",\r\n     \"allscore_signType\" : \"MD5/RSA\",\r\n     \"allscore_priv_key\" : \"\",\r\n     \"allscore_pub_key\" : \"\",\r\n     \"bank_list\" : \"\"\r\n}",
		// 	"allow_deposit_withdraw" => 1,
		// ));
	}

	public function down() {
	}
}
