<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_mobao_kdpay_to_external_system_list_201604251030 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->insert($this->tableName, array(
		// 	"id" => KDPAY_PAYMENT_API, "system_name" => "KDPAY_PAYMENT_API", "system_code" => 'KDPAY',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0,
		// 	"class_name" => "payment_api_kdpay", 'local_path' => 'payment', 'manager' => 'payment_manager'));
		// $this->db->insert($this->tableName, array(
		// 	"id" => MOBAO_PAYMENT_API, "system_name" => "MOBAO_PAYMENT_API", "system_code" => 'MOBAO',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0,
		// 	"class_name" => "payment_api_mobao", 'local_path' => 'payment', 'manager' => 'payment_manager'));
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => KDPAY_PAYMENT_API));
		// $this->db->delete($this->tableName, array('id' => MOBAO_PAYMENT_API));
	}
}
