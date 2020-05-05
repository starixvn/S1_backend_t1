<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_lb_to_game_type_201512031203 extends CI_Migration {
	public function up() {
		$sys = $this->config->item('external_system_map');
		// if (array_key_exists(LB_API, $sys)) {
		$this->db->trans_start();
		$data = array(
			array('game_platform_id' => LB_API,
				'id' => 39,
				'game_type' => 'Keno Game',
				'game_type_lang' => 'lb_keno_game',
				'status' => true,
			),
		);

		$this->db->insert_batch('game_type', $data);
		$this->db->trans_complete();
		// }
	}

	public function down() {
		$this->db->delete('game_type', array('game_platform_id' => LB_API));
	}
}