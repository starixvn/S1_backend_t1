<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_payment_yafu_to_external_system_list_201612081147 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		// $data = array(
		// 	'system_name' => 'YAFU_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'YAFU',
		// 	'class_name' => 'payment_api_yafu',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => 'http://pay.yafupay.com/bank_pay.do',
		// 	'sandbox_key' => '',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '',
		// 	'live_url' => 'http://pay.yafupay.com/bank_pay.do',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '1',
		// 	'id' => YAFU_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);

		// $data = array(
		// 	'system_name' => 'YAFU_WECHAT_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'YAFU_WECHAT',
		// 	'class_name' => 'payment_api_yafu_wechat',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => 'http://pay.yafupay.com/weixin_pay.do',
		// 	'sandbox_key' => '',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '',
		// 	'live_url' => 'http://pay.yafupay.com/weixin_pay.do',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '1',
		// 	'id' => YAFU_WECHAT_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);

		// $data = array(
		// 	'system_name' => 'YAFU_ALIPAY_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'YAFU',
		// 	'class_name' => 'payment_api_yafu_alipay',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => 'http://pay.yafupay.com/alipay_pay.do',
		// 	'sandbox_key' => '',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '',
		// 	'live_url' => 'http://pay.yafupay.com/alipay_pay.do',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '1',
		// 	'id' => YAFU_ALIPAY_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);

	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => YAFU_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => YAFU_WECHAT_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => YAFU_ALIPAY_PAYMENT_API));
	}
}