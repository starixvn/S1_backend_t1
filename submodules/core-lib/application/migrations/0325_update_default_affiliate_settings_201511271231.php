<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_default_affiliate_settings_201511271231 extends CI_Migration {

	public function up() {
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update('operator_settings', array('value' => '{"baseIncomeConfig": "2","minimumPayAmount": "1000","paymentDay": "1","cashback_fee": "100"}'));
	}

	public function down() {
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update('operator_settings', array('value' => ''));
	}
}