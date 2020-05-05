<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bofo_to_bank_list_201511111745 extends CI_Migration {

	public function up() {
		$data = array(
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3001', 'bank_type_order' => 10),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3002', 'bank_type_order' => 20),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3003', 'bank_type_order' => 30),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3004', 'bank_type_order' => 40),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3005', 'bank_type_order' => 50),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3006', 'bank_type_order' => 60),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3009', 'bank_type_order' => 70),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3020', 'bank_type_order' => 80),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3022', 'bank_type_order' => 90),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3026', 'bank_type_order' => 100),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3032', 'bank_type_order' => 110),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3035', 'bank_type_order' => 120),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3036', 'bank_type_order' => 130),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3037', 'bank_type_order' => 140),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3038', 'bank_type_order' => 150),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3039', 'bank_type_order' => 160),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3050', 'bank_type_order' => 170),
			array('external_system_id' => BOFO_PAYMENT_API, 'bank_shortcode' => '3059', 'bank_type_order' => 180),
		);
		$this->db->insert_batch('bank_list', $data);
	}

	public function down() {
		$this->db->where('external_system_id', BOFO_PAYMENT_API)->delete("bank_list");
	}
}

///END OF FILE