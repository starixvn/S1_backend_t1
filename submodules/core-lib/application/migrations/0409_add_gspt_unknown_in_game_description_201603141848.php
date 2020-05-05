<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gspt_unknown_in_game_description_201603141848 extends CI_Migration {
	const FLAG_TRUE = 1;
	private $tableName = 'game_description';

	public function up() {
		$this->db->trans_start();
		$data = array(
		
			array(
				'game_platform_id' => GSPT_API,
				'game_name' => 'opus.unknown',
				'english_name' => 'Unknown GSPT Game',
				'external_game_id' => 'unknown',
				'game_code' => 'unknown',
				'game_type_id' => '96',
			),
		);

		$this->db->insert_batch('game_description', $data);
		$this->db->trans_complete();
	}

	public function down() {
		$game_name = array('gspt.unknown');
		$this->db->where_in('game_name', $game_name);
		$this->db->delete($this->tableName);
	}
}
