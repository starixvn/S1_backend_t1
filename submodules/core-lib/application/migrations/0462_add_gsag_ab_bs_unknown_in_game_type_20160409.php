<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gsag_ab_bs_unknown_in_game_type_20160409 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$this->db->trans_start();

		$data = array(
			GSAG_API => array(
				'game_type' => array(
					'game_type' 		=> 'unknown',
					'game_type_lang' 	=> 'gsag.unknown',
					'status' 			=> self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
				),
				'game_description' => array(
					'game_name' => 'gsag.unknown',
					'english_name' => 'Unknown GSAG Game',
					'external_game_id' => 'unknown',
					'game_code' => 'unknown',
				),
			),
			AB_API => array(
				'game_type' => array(
					'game_type' 		=> 'unknown',
					'game_type_lang' 	=> 'ab.unknown',
					'status' 			=> self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
				),
				'game_description' => array(
					'game_name' => 'ab.unknown',
					'english_name' => 'Unknown AB Game',
					'external_game_id' => 'unknown',
					'game_code' => 'unknown',
				),
			),
			BS_API => array(
				'game_type' => array(
					'game_type' 		=> 'unknown',
					'game_type_lang' 	=> 'bs.unknown',
					'status' 			=> self::FLAG_TRUE,
					'flag_show_in_site' => self::FLAG_FALSE,
				),
				'game_description' => array(
					'game_name' => 'bs.unknown',
					'english_name' => 'Unknown BS Game',
					'external_game_id' => 'unknown',
					'game_code' => 'unknown',
				),
			),
		);

		$game_description_list = array();
		foreach ($data as $game_platform_id => $game_type) {
			$game_type['game_type']['game_platform_id'] = $game_platform_id;
			$this->db->insert('game_type', $game_type['game_type']);
			$game_type_id = $this->db->insert_id();

			$game_type['game_description']['game_platform_id'] = $game_platform_id;
			$game_type['game_description']['game_type_id'] = $game_type_id;
			$this->db->insert('game_description', $game_type['game_description']);

		}

		$this->db->trans_complete();
	}

	public function down() {

		$game_platform_id = array(GSAG_API,AB_API,BS_API);

		$this->db->where_in('game_platform_id', $game_platform_id);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where_in('game_platform_id', $game_platform_id);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');
	}
}
