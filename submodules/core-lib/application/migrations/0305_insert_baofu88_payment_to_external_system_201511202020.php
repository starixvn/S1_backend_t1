<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_baofu88_payment_to_external_system_201511202020 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		//baofu88
		// $this->db->insert($this->tableName, array(
		// 	"id" => BAOFU88_PAYMENT_API, "system_name" => "BAOFU88_PAYMENT_API", "system_code" => 'BAOFU88',
		// 	'system_type' => SYSTEM_PAYMENT, "live_mode" => 0, "live_url" => "http://ag.baofu88.net/GateWay/ReceiveBank.aspx",
		// 	"class_name" => "payment_api_baofu88", 'local_path' => 'payment',
		// 	'manager' => 'payment_manager'));

		// $this->load->model(array('external_system'));
		// $this->external_system->syncCurrentExternalSystem();
	}

	public function down() {
		// $this->db->delete($this->tableName, array('id' => BAOFU88_PAYMENT_API));

		// $this->load->model(array('external_system'));
		// $this->external_system->syncCurrentExternalSystem();
	}
}
