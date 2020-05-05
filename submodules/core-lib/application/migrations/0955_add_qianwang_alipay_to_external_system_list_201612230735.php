<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_qianwang_alipay_to_external_system_list_201612230735 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => QIANWANG_ALIPAY_PAYMENT_API,
		// 	"system_name" => "QIANWANG_ALIPAY_PAYMENT_API",
		// 	"system_code" => "QIANWANG_ALIPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 1,
		// 	"live_url" => "http://apika.10001000.com/chargebank.aspx",
		// 	"sandbox_url" => "http://apika.10001000.com/chargebank.aspx",
		// 	"live_key" => "## API KEY ##",
		// 	"sandbox_key" => "## API KEY ##",
		// 	"class_name" => "payment_api_qianwang_alipay",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"extra_info" => '{ "qianwang_parter" : "##partner code##" }',
		// 	"sandbox_extra_info" => '{ "qianwang_parter" : "##partner code##" }',
		// 	"allow_deposit_withdraw" => 1
		// ));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => QIANWANG_ALIPAY_PAYMENT_API));
	}
}
