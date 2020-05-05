<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_psbc_of_pay365_to_bank_list_201603171353 extends CI_Migration {

	public function up() {
		$this->load->model(array('users', 'external_system'));
		$this->external_system->startTrans();

		$data = array(
			array('external_system_id' => PAY365_PAYMENT_API, 'bank_shortcode' => 'PSBC', 'bank_type_order' => 100, 'status' => 1),
		);
		$this->db->insert_batch('bank_list', $data);

		$superAdmin = $this->users->getSuperAdmin();
		$this->external_system->syncToBanktype($superAdmin->userId);

		$this->external_system->endTransWithSucc();

		//sync payment account
	}

	public function down() {
		$this->db->where('external_system_id', PAY365_PAYMENT_API)
			->where('bank_shortcode', 'PSBC')->delete("bank_list");
	}
}

///END OF FILE