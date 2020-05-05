<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_bank_info_system_features_201711222140 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->insert('system_features', array(
			'name' => 'agent_can_use_balance_wallet',
			'type' => 'partner',
			'enabled' => 0
		));
		$this->db->insert('system_features', array(
			'name' => 'agent_can_have_multiple_bank_accounts',
			'type' => 'partner',
			'enabled' => 0
		));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('system_features', array('name' => 'agent_can_use_balance_wallet'));
		$this->db->delete('system_features', array('name' => 'agent_can_have_multiple_bank_accounts'));
		$this->db->trans_complete();
	}
}
