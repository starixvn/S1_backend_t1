<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_static_sites_company_title_json_201603191530 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->where('site_name', 'default');
		$this->db->or_where('site_name', 'staging');
		$this->db->update('static_sites', array(
			'company_title' => '{"english":"Smartbackend", "chinese":"智能管理后台"}',
		));
		$this->db->trans_complete();
	}

	public function down() {

	}
}