<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_one88_and_lb_unknown_row_to_game_description_201511240742 extends CI_Migration {
	const FLAG_TRUE = 1;
	public function up() {
		$this->db->trans_start();
		$data = array(
			array('game_platform_id' => ONE88_API,
				'game_code' => 'unknown',
				'dlc_enabled' => self::FLAG_TRUE,
				'flash_enabled' => self::FLAG_TRUE,
				'mobile_enabled' => self::FLAG_TRUE,
				'english_name' => 'Unknown 188 Game',
				'external_game_id' => 'unknown',
				'status' => self::FLAG_TRUE,
			),
			array('game_platform_id' => LB_API,
				'game_code' => 'unknown',
				'dlc_enabled' => self::FLAG_TRUE,
				'flash_enabled' => self::FLAG_TRUE,
				'mobile_enabled' => self::FLAG_TRUE,
				'english_name' => 'Unknown LB KENO Game',
				'external_game_id' => 'unknown',
				'status' => self::FLAG_TRUE,
			),
		);
		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->delete('game_description', array('game_platform_id' => ONE88_API, 'game_code' => 'unknown'));
		$this->db->delete('game_description', array('game_platform_id' => LB_API, 'game_code' => 'unknown'));
	}
}