<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_affiliate_setting_value_in_operator_settings_201512111439 extends CI_Migration {

	public function up() {
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update('operator_settings', array('value' => '{"baseIncomeConfig": "2","level_master":"0","minimumPayAmount": "0","paymentDay": "1","cashback_fee": "100"}'));
	}

	public function down() {
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update('operator_settings', array('value' => ''));
	}
}