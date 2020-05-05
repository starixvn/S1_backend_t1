<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_user_guide_link_system_features_201708291035 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->insert('system_features', array(
			'name' => 'user_guide_link',
			'enabled' => 1
		));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('system_features', array('name' => 'user_guide_link'));
		$this->db->trans_complete();
	}

}