<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_pay365_to_bank_list_201602041131 extends CI_Migration {

	public function up() {
		$this->load->model(array('users', 'external_system'));
		$this->external_system->startTrans();

		$data = array(
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'ICBC', 'bank_type_order' => 10, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CMB', 'bank_type_order' => 20, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'ABC', 'bank_type_order' => 30, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CCB', 'bank_type_order' => 40, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'BCOM', 'bank_type_order' => 50, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CIB', 'bank_type_order' => 60, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CMBC', 'bank_type_order' => 70, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CEB', 'bank_type_order' => 80, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'BOC', 'bank_type_order' => 90, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'CITIC', 'bank_type_order' => 100, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'SDB', 'bank_type_order' => 110, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'GDB', 'bank_type_order' => 120, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'SPD', 'bank_type_order' => 130, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'PAB', 'bank_type_order' => 150, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'HXB', 'bank_type_order' => 160, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'HKBEA', 'bank_type_order' => 170, 'status' => 1),
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'BOSH', 'bank_type_order' => 180, 'status' => 1),
		);
		$this->db->insert_batch('bank_list', $data);

		$superAdmin = $this->users->getSuperAdmin();
		$this->external_system->syncToBanktype($superAdmin->userId);

		$this->external_system->endTransWithSucc();

		//sync payment account
	}

	public function down() {
		$this->db->where('external_system_id', PAY365_PAYMENT_API)->delete("bank_list");
	}
}

///END OF FILE