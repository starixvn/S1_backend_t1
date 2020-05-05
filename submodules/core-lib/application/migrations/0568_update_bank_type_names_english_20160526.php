<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_bank_type_names_english_20160526 extends CI_Migration {

	public function up() {
		/*$this->lang->load('main', 'english');
		$this->db->select('bankTypeId');
		$this->db->select('bankName');
		$this->db->from('banktype');
		$query = $this->db->get();
		$bankTypes = $query->result_array();
		foreach ($bankTypes as &$bankType) {
			if (substr($bankType['bankName'], 0, 6) === '_json:') {
				$bankType['bankName'] = json_decode(substr($bankType['bankName'], 6),true);
				$bankType['bankName'][1] = lang($bankType['bankName'][1]);
				$bankType['bankName'] = '_json:' . json_encode($bankType['bankName']);
			}
		}
		$this->db->update_batch('banktype', $bankTypes, 'bankTypeId');*/
	}

	public function down() {
	}
}
