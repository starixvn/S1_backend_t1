<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_loadcard_to_external_system_list_201602121525 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//update sandbox of gopay
		// $data = array(
		// 	'system_name' => 'LOADCARD_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'LOADCARD',
		// 	'class_name' => 'payment_api_loadcard',
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
		// 	'id' => LOADCARD_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);
		//should run sync_external_system and sync_banktype
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => LOADCARD_PAYMENT_API));
	}
}