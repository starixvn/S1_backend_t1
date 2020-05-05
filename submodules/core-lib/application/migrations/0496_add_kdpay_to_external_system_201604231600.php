<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_kdpay_to_external_system_201604231600 extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => KDPAY_PAYMENT_API, "system_name" => "KDPAY_PAYMENT_API", "system_code" => 'KDPAY',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0,
		// 	"class_name" => "payment_api_kdpay", 'local_path' => 'payment', 'manager' => 'payment_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => KDPAY_PAYMENT_API));
	}
}
