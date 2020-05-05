<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_bank_201511050035 extends CI_Migration {

	private $tableName = 'bank_list';

	public function up() {
		$this->db->set('bank_shortcode', 'zhesyh')
			->where_in('bank_type_code', array('00196', '00086'));
		$this->db->update($this->tableName);
	}

	public function down() {
	}
}