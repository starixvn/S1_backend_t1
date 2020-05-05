<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_static_sites_default_site_201603190105 extends CI_Migration {

	public function up() {
		$this->db->trans_start();
		$this->db->where('site_name', 'default');
		$this->db->update('static_sites', array(
			'logo_icon_filepath' => 'og-login-logo.png',
			'company_title' => 'lang.sb',
			'contact_skype' => 'itdesk.smartbackend',
			'contact_email' => 'helpdesk@smartbackend.com',
		));

		$this->db->where('site_name', 'staging');
		$this->db->update('static_sites', array(
			'logo_icon_filepath' => 'og-login-logo.png',
			'company_title' => 'lang.sb',
			'contact_skype' => 'itdesk.smartbackend',
			'contact_email' => 'helpdesk@smartbackend.com',
		));
		$this->db->trans_complete();
	}

	public function down() {

	}
}