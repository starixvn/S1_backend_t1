<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_game_type_201510100601 extends CI_Migration {
	const GAMETYPE_PROGRESSIVE_GAMES = 16;
	private $tableName = 'game_type';
	public function up() {
		$this->db->trans_start();
		$this->db->where('id', self::GAMETYPE_PROGRESSIVE_GAMES);
		$this->db->update($this->tableName, $data = array('game_type_lang' => 'mg_progressives'));
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->delete('game_description', array('game_platform_id' => MG_API));
	}
}