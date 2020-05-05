<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ibc_game_names_and_game_codes_201605191900 extends CI_Migration {


	public function up() {

		$this->db->trans_start();
        

        $sql1 = "SELECT * FROM game_description WHERE game_platform_id = ? AND game_name != ?";
		$query = $this->db->query($sql1, array(IBC_API,'ibc.unknown'));

		foreach ($query->result() as $v) {
			$sql2 = 'UPDATE game_description SET game_name = ? , game_code = ?, external_game_id = ? WHERE id = ?';
	    	$query = $this->db->query($sql2, array($v->english_name, 'ibc.games.'.$v->game_code, 'ibc.games.'.$v->game_code , $v->id));

		}

		$this->db->trans_complete();
	}

	public function down() {

		
		$this->db->trans_start();
        
        $sql1 = "SELECT * FROM game_description WHERE game_platform_id = ? AND game_name != ?";
		$query = $this->db->query($sql1, array(IBC_API,'ibc.unknown'));

		foreach ($query->result() as $v) {
			$game_name = str_replace('ibc.games.','',  $v->game_code);
			$sql2 = 'UPDATE game_description SET game_name = ? , game_code = ?, external_game_id = ? WHERE id = ?';
	    	$query = $this->db->query($sql2, array( $v->game_code, $game_name, $game_name, $v->id));

		}

		$this->db->trans_complete();
	}
}
