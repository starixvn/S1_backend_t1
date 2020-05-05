<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unknown_game_description_and_type_for_ags_201609280512 extends CI_Migration {
	
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		$this->db->trans_start();

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> AGBBIN_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'agbbin.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> AGBBIN_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'agbbin.unknown',
			'english_name' 		=> 'Unknown AGBBIN GAME',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> AGHG_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'aghg.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> AGHG_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'aghg.unknown',
			'english_name' 		=> 'Unknown AGHG GAME',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> AGPT_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'agpt.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> AGPT_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'agpt.unknown',
			'english_name' 		=> 'Unknown AGPT GAME',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> AGSHABA_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'agshaba.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> AGSHABA_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'agshaba.unknown',
			'english_name' 		=> 'Unknown AGSHABA GAME',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		$this->db->insert('game_type', array(
			'game_platform_id' 	=> AGIN_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'agin.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> AGIN_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'agin.unknown',
			'english_name' 		=> 'Unknown AGIN GAME',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));




		$this->db->trans_complete();
	}

	public function down() {


		$this->db->trans_start();

		$this->db->where('game_platform_id', AGBBIN_API);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', AGBBIN_API);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');


		$this->db->where('game_platform_id', AGHG_API);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', AGHG_API);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');


		$this->db->where('game_platform_id', AGPT_API);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', AGPT_API);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');

		$this->db->where('game_platform_id', AGSHABA_API);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', AGSHABA_API);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');

		$this->db->where('game_platform_id', AGIN_API);
		$this->db->where('game_code', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', AGIN_API);
		$this->db->where('game_type', 'unknown');
		$this->db->delete('game_type');





		$this->db->trans_complete();
	}
}
