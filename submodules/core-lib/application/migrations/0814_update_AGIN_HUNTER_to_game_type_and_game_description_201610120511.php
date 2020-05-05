<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_AGIN_HUNTER_to_game_type_and_game_description_201610120511 extends CI_Migration {


	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	
	public function up() {

		$this->db->trans_start();


		$sql = "SELECT * FROM game_type  WHERE game_platform_id = ? AND game_type = ? AND game_type_lang = ?";
		$query = $this->db->query($sql,array(AGIN_API,'HSR','agin.hunter'));
		$row = $query->row();
		$game_type_id = @$row->id;

		if(!$game_type_id){

			$this->db->insert('game_type', array(
				'game_platform_id' => AGIN_API,
				'game_type' => 'HSR',
				'game_type_lang' => 'agin.hunter',
				'status' => self::FLAG_TRUE,
				'flag_show_in_site' => 1,
				));

		}
		$game_type_id = $this->db->insert_id();

		$sql2 = "UPDATE game_description SET game_type_id = ? WHERE game_name = ? AND game_platform_id = ?";
		
		$query = $this->db->query($sql2,array($game_type_id,'agin.hunter',AGIN_API));

		$this->db->trans_complete();

	}

	public function down() {
		//
	}
}