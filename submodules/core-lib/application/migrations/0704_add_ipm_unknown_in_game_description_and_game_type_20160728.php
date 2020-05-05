<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ipm_unknown_in_game_description_and_game_type_20160728 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$this->db->trans_start();

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> SPORTSBOOK_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'ipm.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> SPORTSBOOK_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'ipm.unknown',
			'english_name' 		=> 'Unknown IPM Game',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		$this->db->trans_complete();
	}

	public function down() {

		$game_platform_id = SPORTSBOOK_API;

		$this->db->trans_start();

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');

		$this->db->trans_complete();
	}
}
