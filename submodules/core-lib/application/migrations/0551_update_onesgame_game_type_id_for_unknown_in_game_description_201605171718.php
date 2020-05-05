<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_onesgame_game_type_id_for_unknown_in_game_description_201605171718 extends CI_Migration {
	


	public function up() {

		$this->db->trans_start();

			//Find onesgame.unknown in game_type type table where platform_id = 21  get id
			$sql1= "SELECT id FROM game_type  WHERE game_platform_id = ? AND  game_type = ? AND game_type_lang = ? ";
					$query = $this->db->query($sql1, array(ONESGAME_API,'unknown', 'onesgame.unknown'));

					$game_type_id = @$query->row_array()['id'];
			//Find ones.unknown in game_description table where platform_id = 21  and game_name = ones.unknown
			$sql2= "SELECT id FROM game_description  WHERE game_platform_id = ? AND  game_name = ? AND game_code = ? ";
					$query2 = $this->db->query($sql2, array(ONESGAME_API,'onesgame.unknown', 'unknown'));

					$game_description_id1  = @$query2->row_array()['id'];


			#SOMETIME IT HAS 2 ENTRIES  ones.unknown and onesgame.unknown
		   if($game_description_id1){
		   		#IF IT HAS GAME DESCRIPTION ID UPDATE IT
		   		$sql3 = 'UPDATE game_description SET game_type_id = ? WHERE game_platform_id = ? AND id = ?';
		     	$query3 = $this->db->query($sql3, array($game_type_id, ONESGAME_API, $game_description_id));

		     	#CHECK THE OTHER ones.unknown
		     	$sql4= "SELECT id FROM game_description  WHERE game_platform_id = ? AND  game_name = ? AND game_code = ? ";
				$query4 = $this->db->query($sql4, array(ONESGAME_API,'ones.unknown', 'unknown'));

				   $game_description_id2  = @$query4->row_array()['id'];
				   #IF IT HAS ones.unknown Delete it
				   if($game_description_id2){
				   		$this->db->delete('game_description', array('id' => $game_description_id2, 'game_name' =>'ones.unknown'));
				   }

		   }else{

		     	//Find onesgame.unknown in game_type type table where platform_id = 21  get id
			    $sql1= "SELECT id FROM game_type  WHERE game_platform_id = ? AND  game_type = ? AND game_type_lang = ? ";
				$query = $this->db->query($sql1, array(ONESGAME_API,'unknown', 'onesgame.unknown'));

					$game_type_id = @$query->row_array()['id'];

		   		#CHECK THE OTHER ones.unknown
		     	$sql5= "SELECT id FROM game_description  WHERE game_platform_id = ? AND  game_name = ? AND game_code = ? ";
				$query5 = $this->db->query($sql5, array(ONESGAME_API,'ones.unknown', 'unknown'));

				$game_description_id3  = @$query5->row_array()['id'];

				if($game_description_id3){
					//Update the game_type_id of onesgame unknown
			    	$sql3 = 'UPDATE game_description SET game_type_id = ?, game_name = ? WHERE game_platform_id = ? AND id = ?';
	    			$query3 = $this->db->query($sql3, array($game_type_id,'onesgame.unknown', ONESGAME_API, $game_description_id3));
				}
				

		   }	


			



	   $this->db->trans_complete();

	}

	public function down() {

		$this->db->trans_start();

			//Find onesgame.unknown in game_type type table where platform_id = 21  get id
			$sql1= "SELECT id FROM game_type  WHERE game_platform_id = ? AND  game_type = ? AND game_type_lang = ? ";
					$query = $this->db->query($sql1, array(ONESGAME_API,'unknown', 'onesgame.unknown'));

					$game_type_id = @$query->row_array()['id'];
			//Find ones.unknown in game_description table where platform_id = 21  and game_name = ones.unknown
			$sql2= "SELECT id FROM game_description  WHERE game_platform_id = ? AND  game_name = ? AND game_code = ? ";
					$query2 = $this->db->query($sql2, array(ONESGAME_API,'onesgame.unknown', 'unknown'));

					$game_description_id  = @$query2->row_array()['id'];

			//Update the game_type_id of onesgame unknown
			$sql3 = 'UPDATE game_description SET game_type_id = ? WHERE game_platform_id = ? AND id = ?';

			$query3 = $this->db->query($sql3, array($game_type_id, ONESGAME_API, $game_description_id));
			
	   $this->db->trans_complete();

	}
}
