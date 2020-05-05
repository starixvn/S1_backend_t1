<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bank_list_rows_for_gopay20150910 extends CI_Migration {

	public function up() {
		//init data
		// $this->db->insert('external_system', array("id" => GOPAY_PAYMENT_API, "system_name" => "GOPAY_PAYMENT_API")); //urls not yet

		// $data = array(
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CCB', 'bank_type_order' => 1),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CMB', 'bank_type_order' => 2),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'ICBC', 'bank_type_order' => 3),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'BOC', 'bank_type_order' => 4),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'ABC', 'bank_type_order' => 5),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'BOCOM', 'bank_type_order' => 6),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CMBC', 'bank_type_order' => 7),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'HXBC', 'bank_type_order' => 8),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CIB', 'bank_type_order' => 9),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'SPDB', 'bank_type_order' => 10),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'GDB', 'bank_type_order' => 11),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CITIC', 'bank_type_order' => 12),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'CEB', 'bank_type_order' => 13),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'PSBC', 'bank_type_order' => 14),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'TCCB', 'bank_type_order' => 15),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'BOS', 'bank_type_order' => 16),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'PAB', 'bank_type_order' => 17),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'NBCB', 'bank_type_order' => 18),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'NJCB', 'bank_type_order' => 19),
		// 	array('external_system_id' => GOPAY_PAYMENT_API, 'bank_shortcode' => 'BOBJ', 'bank_type_order' => 20),
		// );
		// $this->db->insert_batch('bank_list', $data);
	}

	public function down() {
		$this->db->where('external_system_id', GOPAY_PAYMENT_API)->delete("bank_list");
		// $this->db->delete_batch('bank_list', $this->up->$date);
		$this->db->delete('external_system', array("id" => GOPAY_PAYMENT_API));
	}
}

///END OF FILE