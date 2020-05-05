<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_external_system_list_extra_info_201605011530 extends CI_Migration {
	private $tableName = 'external_system_list';

	public function up() {
		// $data = array(
		//    array(
		// 	  'id' => WFT_API,
		// 	  'extra_info' => '{"prefix_for_username": "wft", "balance_in_game_log": true, "adjust_datetime_minutes": 0, "wft_currency" : "RMB", "wft_login_host" : "<host>", "wft_login_lang" : "ZH-CN"}',
		// 	  'sandbox_extra_info' => '{"prefix_for_username": "wft", "balance_in_game_log": true, "adjust_datetime_minutes": 0, "wft_currency" : "RMB", "wft_login_host" : "<host>", "wft_login_lang" : "ZH-CN"}',
		//    ),
		//    array(
		// 	  'id' => MOBAO_PAYMENT_API,
		// 	  'extra_info' => '{"mobao_apiVersion" : "1.0.0.0", "mobao_platformID" : "", "mobao_merchNo" : ""}',
		// 	  'sandbox_extra_info' => '{"mobao_apiVersion" : "1.0.0.0", "mobao_platformID" : "", "mobao_merchNo" : ""}',
		//    ),
		//    array(
		// 	  'id' => KDPAY_PAYMENT_API,
		// 	  'extra_info' => '{"kdpay_P_UserId" : ""}',
		// 	  'sandbox_extra_info' => '{"kdpay_P_UserId" : ""}',
		//    ),
		// );

		// $this->db->update_batch($this->tableName, $data, 'id');
	}

	public function down() {

	}
}