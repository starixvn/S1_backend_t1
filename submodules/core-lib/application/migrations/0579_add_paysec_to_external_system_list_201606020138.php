<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_paysec_to_external_system_list_201606020138 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//update sandbox of gopay
		// $data = array(
		// 	'system_name' => 'PAYSEC_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'PAYSEC',
		// 	'class_name' => 'payment_api_paysec',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => '',
		// 	'sandbox_key' => '',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '',
		// 	'live_url' => 'https://pay.paysec.com/GUX/GPay',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '1',
		// 	'id' => PAYSEC_PAYMENT_API,
		// 	'extra_info' => '{"token_url": "https://pay.paysec.com/GUX/GPost"}',
		// );
		// $this->db->insert($this->tableName, $data);
		//should run sync_external_system
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => PAYSEC_PAYMENT_API));
	}
}