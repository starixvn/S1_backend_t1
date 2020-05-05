<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ones_and_opus_unknown_in_game_description_201603111146 extends CI_Migration {
	const FLAG_TRUE = 1;
	private $tableName = 'game_description';

	public function up() {
		$this->db->trans_start();
		$data = array(
			array(
				'game_platform_id' => ONESGAME_API,
				'game_name' => 'ones.unknown',
				'english_name' => 'Unknown Ones Game',
				'external_game_id' => 'unknown',
				'game_code' => 'unknown',
				'game_type_id' => '49',
			),
			array(
				'game_platform_id' => OPUS_API,
				'game_name' => 'opus.unknown',
				'english_name' => 'Unknown Opus Game',
				'external_game_id' => 'unknown',
				'game_code' => 'unknown',
				'game_type_id' => '56',
			),
		);

		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
	}

	public function down() {
		$game_name = array('opus.unknown', 'ones.unknown');
		$this->db->where_in('game_name', $game_name);
		$this->db->delete($this->tableName);
	}
}
