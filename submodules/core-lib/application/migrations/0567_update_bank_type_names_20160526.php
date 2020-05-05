<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_bank_type_names_20160526 extends CI_Migration {

	public function up() {
		
		/*$this->db->select('bankTypeId');
		$this->db->select('bankName');
		$this->db->from('banktype');
		$query = $this->db->get();
		$bankTypes = $query->result_array();
		foreach ($bankTypes as &$bankType) {
			if (substr($bankType['bankName'], 0, 6) != '_json:') {
				$bankType['bankName'] = '_json:' . json_encode(array(
					"1" => $bankType['bankName'],
					"2" => $bankType['bankName'],
				));
			}
		}
		$this->db->update_batch('banktype', $bankTypes, 'bankTypeId');*/
	}

	public function down() {
	}
}
