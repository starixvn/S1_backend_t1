<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_bbin_to_game_type_201511120536 extends CI_Migration {
	const FLAG_TRUE = 1;
	public function up() {
		$sys = $this->config->item('external_system_map');
		// if (array_key_exists(BBIN_API, $sys)) {
		$this->db->trans_start();
		$data = array(
			array('game_platform_id' => BBIN_API,
				'id' => 33,
				'game_type' => 'Sports Game',
				'game_type_lang' => 'bbin_sports_game',
				'status' => self::FLAG_TRUE,
			),
			array('game_platform_id' => BBIN_API,
				'id' => 34,
				'game_type' => 'Lottery Game',
				'game_type_lang' => 'bbin_lottery_game',
				'status' => self::FLAG_TRUE,
			),
			array('game_platform_id' => BBIN_API,
				'id' => 35,
				'game_type' => '3D Hall Game',
				'game_type_lang' => 'bbin_3dhall_game',
				'status' => self::FLAG_TRUE,
			),
			array('game_platform_id' => BBIN_API,
				'id' => 36,
				'game_type' => 'Live Game',
				'game_type_lang' => 'bbin_live_game',
				'status' => self::FLAG_TRUE,
			),
			array('game_platform_id' => BBIN_API,
				'id' => 37,
				'game_type' => 'Casino Game',
				'game_type_lang' => 'bbin_casino_game',
				'status' => self::FLAG_TRUE,
			),
		);

		$this->db->insert_batch('game_type', $data);
		$this->db->trans_complete();
		// }
	}

	public function down() {
		$this->db->delete('game_type', array('game_platform_id' => BBIN_API));
	}
}