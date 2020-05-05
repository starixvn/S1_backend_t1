<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_gd_game_names_201605031700 extends CI_Migration {


	public function up() {

		$this->db->trans_start();
        


        $sql1 = "SELECT id, game_code, english_name FROM game_description WHERE game_platform_id = ? AND game_name != ?";
		$query = $this->db->query($sql1, array(GD_API,'gd.unknown'));

		foreach ($query->result() as $v) {
			$sql2 = 'UPDATE game_description SET game_name = ? WHERE id = ?';
	    	$query = $this->db->query($sql2, array($v->english_name,$v->id));

		}

		$this->db->trans_complete();
	}

	public function down() {

		$this->db->trans_start();
        
        $sql1 = "SELECT id, game_code, english_name FROM game_description WHERE game_platform_id = ? AND game_name != ?";
		$query = $this->db->query($sql1, array(GD_API,'gd.unknown'));

		foreach ($query->result() as $v) {
			$sql2 = 'UPDATE game_description SET game_name = ? WHERE id = ?';
	    	$query = $this->db->query($sql2, array('gd.'.$v->game_code,$v->id));

		}

		$this->db->trans_complete();
	}
}
