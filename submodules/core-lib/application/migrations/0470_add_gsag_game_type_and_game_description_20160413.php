<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gsag_game_type_and_game_description_20160413 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$this->db->trans_start();

		$data = array(
			array(
				'game_type' 		=> 'BR',
				'game_type_lang' 	=> 'gsag.live',
				'status' 			=> self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
			), array(
				'game_type' 		=> 'EBR',
				'game_type_lang' 	=> 'gsag.egame',
				'status' 			=> self::FLAG_TRUE,
				'flag_show_in_site' => self::FLAG_TRUE,
			),
		);

		$game_description_list = array();
		foreach ($data as $game_type) {
			$game_type['game_platform_id'] = GSAG_API;
			$this->db->insert('game_type', $game_type);
			$game_type_id = $this->db->insert_id();

			$this->db->select('game_description.game_name,game_description.english_name,game_description.external_game_id,game_description.game_code');
			$this->db->from('game_description');
			$this->db->join('game_type','game_type.id = game_description.game_type_id','left');
			$this->db->where('game_description.game_platform_id', AG_API);			
			$this->db->where('game_type.game_type', $game_type['game_type']);
			$this->db->where('game_description.game_code !=', 'unknown');
			$query = $this->db->get();
			
			$game_list = $query->result_array();

			foreach ($game_list as &$game) {
				$game['game_platform_id'] = GSAG_API;
				$game['game_type_id'] = $game_type_id;
				$game['game_name'] = str_replace('ag.', 'gsag.', $game['game_name']);
				$this->db->insert('game_description', $game);
			}
		}

		$this->db->trans_complete();
	}

	public function down() {

		$game_platform_id = GSAG_API;

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->delete('game_type');
	}
}
