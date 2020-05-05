<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_payment_fn139_to_external_system_list_201612130119 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		// $data = array(
		// 	'system_name' => 'FN139_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'FN139',
		// 	'class_name' => 'payment_api_fn139',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => '',
		// 	'sandbox_key' => '',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '',
		// 	'live_url' => '',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '1',
		// 	'allow_deposit_withdraw'=>'1',
		// 	'id' => FN139_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);

	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => FN139_PAYMENT_API));
	}
}