<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_one88_to_game_type_201511250629 extends CI_Migration {
	const FLAG_TRUE = 1;
	public function up() {
		// $sys = $this->config->item('external_system_map');
		// if (array_key_exists(ONE88_API, $sys)) {
		$this->db->trans_start();
		$data = array(
			array('game_platform_id' => ONE88_API,
				'id' => '38',
				'game_type' => 'Sports Game',
				'game_type_lang' => 'one88_sports_game',
				'status' => self::FLAG_TRUE,
			),
		);
		$this->db->insert_batch('game_type', $data);

		$game_type = array(
			array(
				'game_platform_id' => ONE88_API,
				'game_type_id' => '38',
			),
		);
		$this->db->update_batch('game_description', $game_type, 'game_platform_id');

		$this->db->trans_complete();
		// }
	}

	public function down() {
		$this->db->delete('game_type', array('game_platform_id' => ONE88_API));
	}
}