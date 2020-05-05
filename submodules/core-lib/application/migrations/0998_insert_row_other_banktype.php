<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_row_other_banktype extends CI_Migration {

	public function up() {
		$this->db->insert('banktype', array(
			'status' => 'not active',
			'bankName' => 'other',
			'bank_code' => 'other'
		));
	}

	public function down() {
	}
}