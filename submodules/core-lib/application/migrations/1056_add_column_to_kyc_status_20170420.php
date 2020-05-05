<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_kyc_status_20170420 extends CI_Migration {

	public function up() {
		$fields = array(
			'kyc_lvl' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
		);
		$this->dbforge->add_column('kyc_status', $fields);

		$data = array(
			array(
				'id' => 1,
				'kyc_lv' => 'Not Certified'
			),
			array(
				'id' => 2,
				'kyc_lv' => 'Low'
			),
			array(
				'id' => 3,
				'kyc_lv' => 'Medium'
			),
			array(
				'id' => 4,
				'kyc_lv' => 'High'
			),
		);

		$this->db->trans_start();

		foreach ($data as $datai) {

			$this->db->where('id', $datai['id'])
						->update('kyc_status', array(
							'kyc_lvl' => $datai['kyc_lv']
						));
		}

		$this->db->trans_complete();
	}

	public function down() {
		$this->dbforge->drop_column('kyc_status', 'kyc_lvl');
	}
}