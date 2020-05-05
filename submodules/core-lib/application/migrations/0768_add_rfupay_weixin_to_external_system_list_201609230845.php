<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rfupay_weixin_to_external_system_list_201609230845 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => RFUPAY_WEIXIN_PAYMENT_API,
		// 	"system_name" => "RFUPAY_WEIXIN_PAYMENT_API",
		// 	"system_code" => "RFUPAY",
		// 	"system_type" => SYSTEM_PAYMENT,
		// 	"live_mode" => 0,
		// 	"class_name" => "payment_api_rfupay_weixin",
		// 	"local_path" => "payment",
		// 	"manager" => "payment_manager",
		// 	"live_key" => "",
		// 	"sandbox_key" => "",
		// 	"live_url" => "http://payment.rfupay.com/prod/commgr/control/inPayService",
		// 	"sandbox_url" => "http://payment.rfupay.com/prod/commgr/control/inPayService",
		// 	"extra_info" => "{\r\n\t\"rfupay_goods\" : \"##Order Prefix##\",\r\n\t\"rfupay_partyId\" : \"##Party ID##\",\r\n\t\"rfupay_accountId\" : \"##Account ID##\"\r\n}",
		// 	"sandbox_extra_info" => "{\r\n\t\"rfupay_goods\" : \"##Order Prefix##\",\r\n\t\"rfupay_partyId\" : \"##Party ID##\",\r\n\t\"rfupay_accountId\" : \"##Account ID##\"\r\n}",
		// 	"allow_deposit_withdraw" => 1
		// ));

		// # also update the classname used by RFUPay Gateway API
		// $data = array(
		// 	'class_name' => "payment_api_rfupay_gateway",
		// );
		// $this->db->where('id', RFUPAY_PAYMENT_API);
		// $this->db->update($this->tableName, $data);
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => RFUPAY_WEIXIN_PAYMENT_API));
	}
}
