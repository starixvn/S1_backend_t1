<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_bankname_in_banktype extends CI_Migration {

	public function up() {
		$this->db->query("UPDATE banktype SET bankName = CONCAT('bank_type',bankTypeId)");
	}

	public function down() {
		$this->lang->load('main', 'english');
		$list = $this->db->query("SELECT bankTypeId, bankName FROM banktype")->result_array();
		foreach ($list as $value) {
			$this->db->query("UPDATE banktype SET bankName = ? WHERE bankTypeId = ?", [
				lang($value['bankName']),
				$value['bankTypeId'],
			]);
		}
	}
}