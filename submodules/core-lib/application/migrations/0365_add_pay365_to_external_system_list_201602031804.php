<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_pay365_to_external_system_list_201602031804 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//update sandbox of gopay
		// $data = array(
		// 	'system_name' => 'PAY365_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'PAY365',
		// 	'class_name' => 'payment_api_pay365',
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
		// 	'id' => PAY365_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);
		//should run sync_external_system
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => PAY365_PAYMENT_API));
	}
}