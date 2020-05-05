<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_include_company_name_in_title_system_features_201708300015 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->insert('system_features', array(
			'name' => 'include_company_name_in_title',
			'enabled' => 0
		));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('system_features', array('name' => 'include_company_name_in_title'));
		$this->db->trans_complete();
	}

}