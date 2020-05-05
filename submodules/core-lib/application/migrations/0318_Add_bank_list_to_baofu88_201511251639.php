<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bank_list_to_baofu88_201511251639 extends CI_Migration {

	public function up() {
		$this->load->model(array('users', 'external_system'));
		$this->external_system->startTrans();

		$data = array(
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'ICBC', 'bank_type_order' => 10, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'CMBCHINA', 'bank_type_order' => 20, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'ABC', 'bank_type_order' => 30, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'CCB', 'bank_type_order' => 40, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'BOCO', 'bank_type_order' => 50, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'CIB', 'bank_type_order' => 60, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'CMBC', 'bank_type_order' => 70, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'CEB', 'bank_type_order' => 80, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'BOC', 'bank_type_order' => 90, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'ECITIC', 'bank_type_order' => 100, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'SDB', 'bank_type_order' => 110, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'GDB', 'bank_type_order' => 120, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'SPDB', 'bank_type_order' => 130, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'POST', 'bank_type_order' => 140, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'PINGANBANK', 'bank_type_order' => 150, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'HXB', 'bank_type_order' => 160, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'HKBEA', 'bank_type_order' => 170, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'ALL', 'bank_type_order' => 180, 'status' => 1),
			array('external_system_id' => BAOFU88_PAYMENT_API, 'bank_shortcode' => 'OnLine', 'bank_type_order' => 190, 'status' => 1),
		);
		$this->db->insert_batch('bank_list', $data);

		$superAdmin = $this->users->getSuperAdmin();
		$this->external_system->syncToBanktype($superAdmin->userId);

		$this->external_system->endTransWithSucc();

		//sync payment account
	}

	public function down() {
		$this->db->where('external_system_id', BAOFU88_PAYMENT_API)->delete("bank_list");
	}
}

///END OF FILE