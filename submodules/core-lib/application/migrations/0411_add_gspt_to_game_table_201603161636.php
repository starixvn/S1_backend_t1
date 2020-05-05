<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gspt_to_game_table_201603161636 extends CI_Migration {

	private $tableName = 'game';

	public function up() {
		$this->db->trans_start();

		
		$data = array(
			   'gameId' => '22' ,
			   'game' => 'GSPT' 
			 	);

        $this->db->insert($this->tableName, $data); 
			
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->delete($this->tableName, array('gameId' => '22')); 
	}
}
