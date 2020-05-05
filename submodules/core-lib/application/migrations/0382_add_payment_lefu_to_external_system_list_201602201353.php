<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_payment_lefu_to_external_system_list_201602201353 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//update sandbox of gopay
		// $data = array(
		// 	'system_name' => 'LEFU_PAYMENT_API',
		// 	'system_type' => SYSTEM_PAYMENT,
		// 	'system_code' => 'LEFU',
		// 	'class_name' => 'payment_api_lefu',
		// 	'local_path' => 'payment',
		// 	'manager' => 'payment_manager',
		// 	'game_platform_rate' => 100,
		// 	'sandbox_url' => 'http://qa.lefu8.com/gateway/trade.htm',
		// 	'sandbox_key' => '8614271579',
		// 	'sandbox_account' => '',
		// 	'sandbox_secret' => '5E1524AABA00627C87DD2E28726AA785',
		// 	'live_url' => '',
		// 	'live_key' => '',
		// 	'live_account' => '',
		// 	'live_secret' => '',
		// 	'live_mode' => '0',
		// 	'id' => LEFU_PAYMENT_API,
		// );
		// $this->db->insert($this->tableName, $data);
		//should run sync_external_system and sync_banktype
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => LEFU_PAYMENT_API));
	}
}