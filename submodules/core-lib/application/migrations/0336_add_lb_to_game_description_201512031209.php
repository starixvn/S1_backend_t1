<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_lb_to_game_description_201512031209 extends CI_Migration {
	public function up() {
		//check config first
		$sys = $this->config->item('external_system_map');
		// if (array_key_exists(LB_API, $sys)) {
		$this->db->trans_start();

		$data = array(
			array('game_platform_id' => LB_API,
				'game_type_id' => '39',
				'game_name' => 'lb.Keno',
				'english_name' => 'Keno',
				'external_game_id' => 'lbkeno',
				'game_code' => 'lbkeno',
				'flash_enabled' => true,
				'flag_show_in_site' => true,
				'status' => true),
		);

		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
		// }
	}

	public function down() {
		$this->db->delete('game_description', array('game_platform_id' => LB_API));
	}
}