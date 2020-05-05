<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_tier_comm_pattern_and_comm_optional_system_features_201711170602 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->insert('system_features', array(
			'name' => 'agent_tier_comm_pattern',
			'type' => 'partner',
			'enabled' => 0
		));
		$this->db->insert('system_features', array(
			'name' => 'agent_comm_optional',
			'type' => 'partner',
			'enabled' => 0
		));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('system_features', array('name' => 'agent_tier_comm_pattern'));
		$this->db->delete('system_features', array('name' => 'agent_comm_optional'));
		$this->db->trans_complete();
	}
}
