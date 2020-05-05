<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_31_to_operator_settings extends CI_Migration {

	public function up() {
		// $this->db->trans_start();
		// $data = array(
		// 	'name' => 'blocked_game_setting',
		// 	'value' => '{"' . PT_API . '": "0","' . AG_API . '": "0","' . MG_API . '": "0","' . NT_API . '": "0","' . BBIN_API . '": "0","' . LB_API . '": "0","' . ONE88_API . '": "0"}',
		// 	'note' => 'Blocked Game Settings Default in json format',
		// );

		// $this->db->insert('operator_settings', $data);
		// $this->db->trans_complete();
	}

	public function down() {
		// $this->db->delete('blocked_game_setting', array('name' => 'blocked_game_setting'));
	}
}