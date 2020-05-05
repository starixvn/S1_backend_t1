<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_bank_order_in_bank_list extends CI_Migration {

	public function up() {
		//change order
		$data = array(
			array(
				'bank_type_code' => '00004',
				'bank_type_order' => 2,
			),
			array(
				'bank_type_code' => '00083',
				'bank_type_order' => 4,
			),
			array(
				'bank_type_code' => '00015',
				'bank_type_order' => 6,
			),
			array(
				'bank_type_code' => '00017',
				'bank_type_order' => 8,
			),
			array(
				'bank_type_code' => '00051',
				'bank_type_order' => 10,
			),
			array(
				'bank_type_code' => '00021',
				'bank_type_order' => 12,
			),
			array(
				'bank_type_code' => '00016',
				'bank_type_order' => 14,
			),
			array(
				'bank_type_code' => '00052',
				'bank_type_order' => 16,
			),
			array(
				'bank_type_code' => '00087',
				'bank_type_order' => 18,
			),
		);

		$this->db->update_batch('bank_list', $data, 'bank_type_code');

	}

	public function down() {
	}
}

///END OF FILE