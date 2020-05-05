<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_defaults_to_affiliate_settings_201511250633 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		// $value = '{"baseIncomeConfig": "2","minimumPayAmount": "1000","paymentDay": "1","cashback_fee": ""}';
		// $data = array('value' => $value);
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update($this->tableName, $data);
	}

	public function down() {
		// $value = '';
		// $data = array('value' => $value);
		// $this->db->where('name', 'affiliate_settings');
		// $this->db->update($this->tableName, $data);
	}
}