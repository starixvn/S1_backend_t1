<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201705151825 extends CI_Migration {

	public function up() {

		$data = array(array(
				'name' => 'vip_welcome_text',
				'value' => '{"main":"Welcome To The King Palace","sub":"Please join a group"}'
			));

		$this->db->insert_batch('operator_settings', $data); 		
	}

	public function down() {
		$this->db->where('name', 'vip_welcome_text');
        $this->db->delete('operator_settings');
	}
}