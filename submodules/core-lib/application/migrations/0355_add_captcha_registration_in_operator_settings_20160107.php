<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_captcha_registration_in_operator_settings_20160107 extends CI_Migration {

	public function up() {
		// $this->db->insert('operator_settings', array(
		// 	'name' => 'captcha_registration',
		// 	'value' => 1,
		// ));
	}

	public function down() {
		// $this->db->delete('operator_settings', array('name' => 'captcha_registration'));
	}
}