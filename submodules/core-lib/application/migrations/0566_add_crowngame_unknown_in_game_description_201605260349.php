<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_crowngame_unknown_in_game_description_201605260349 extends CI_Migration {
	const FLAG_TRUE = 1;
	public function up() {
		$this->db->trans_start();
		$this->db->insert('game_type', array(
			'game_platform_id' => CROWN_API,
			'game_type' => 'unknown',
			'game_type_lang' => 'crowngame.unknown',
			'status' => self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));
		$lastId = $this->db->insert_id();

		$this->db->insert('game_description', array(
			'game_type_id' => $lastId,
			'game_name' => 'crowngame.unknown',
			'game_platform_id' => CROWN_API,
			'game_code' => 'unknown'));

		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' => CROWN_API, 'game_type' => 'unknown'));
		$this->db->delete('game_description', array('game_platform_id' => CROWN_API));
		$this->db->trans_complete();
	}
}
