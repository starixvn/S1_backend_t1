<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201705080253 extends CI_Migration {

	public function up() {
		$query = $this->db->query("SELECT * FROM operator_settings WHERE name = 'sys_default_logo'");
		$result= $query->row_array();

		if ($result) {
			return false;
		}

		$data = array(array(
				'name' => 'sys_default_logo',
				'value' => '{"path":"uploaded_logo\/","filename":"","use_sys_default":true}'
			));

		$this->db->insert_batch('operator_settings', $data); 		
	}

	public function down() {
		$this->db->where('name', 'sys_default_logo');
        $this->db->delete('operator_settings');
	}
}