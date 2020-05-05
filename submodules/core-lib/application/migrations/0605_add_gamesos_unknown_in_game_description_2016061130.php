<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gamesos_unknown_in_game_description_2016061130 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	
	public function up() {
		
		$this->db->trans_start();
		
		$game_type_data = array(
			'game_platform_id' => GAMESOS_API,
			'game_type' => 'unknown',
			'game_type_lang' => 'gamesos.unknown',
			'status' => self::FLAG_TRUE,
			'flag_show_in_site' =>   self::FLAG_TRUE
			);

		$this->db->insert('game_type', $game_type_data);


		$lastId =  $this->db->insert_id();

		$game_description_data = array(
                'game_type_id' => $lastId ,
                'game_name' => 'gamesos.unknown',
                'game_platform_id' => GAMESOS_API,
				'game_code' => 'unknown',
				'dlc_enabled' => self::FLAG_TRUE,
				'flash_enabled' => self::FLAG_TRUE,
				'mobile_enabled' => self::FLAG_TRUE,
				'english_name' => 'Unknown GAMESOS Game',
				'external_game_id' => 'unknown',
				'status' => self::FLAG_TRUE,
               );

		$this->db->insert('game_description', $game_description_data );
		$this->db->trans_complete();

	}

	public function down() {
		
		$this->db->delete('game_description', array('game_platform_id' => GAMESOS_API, 'game_code' => 'unknown'));
		$this->db->delete('game_type', array('game_platform_id' => GAMESOS_API, 'game_type' => 'unknown'));
		
	}
}



