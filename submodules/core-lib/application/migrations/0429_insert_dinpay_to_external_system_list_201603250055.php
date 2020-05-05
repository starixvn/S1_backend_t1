<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_dinpay_to_external_system_list_201603250055 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => DINPAY_PAYMENT_API, "system_name" => "DINPAY_PAYMENT_API", "system_code" => 'DINPAY',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0,
		// 	"class_name" => "payment_api_dinpay", 'local_path' => 'payment', 'manager' => 'payment_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => DINPAY_PAYMENT_API));
	}
}
