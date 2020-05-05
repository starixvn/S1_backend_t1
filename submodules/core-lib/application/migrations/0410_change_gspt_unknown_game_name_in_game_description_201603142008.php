<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_gspt_unknown_game_name_in_game_description_201603142008 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->db->trans_start();

		$data = array(
               'game_name' => 'gspt.unknown'
              );
	
		$where = array(
				'game_platform_id'=> GSPT_API,
				'game_name'=> 'opus.unknown'
			);

		$this->db->where($where);
		$this->db->update($this->tableName, $data); 
		
		$this->db->trans_complete();
	}

	public function down() {
		$game_name = array('gspt.unknown');
		$this->db->where_in('game_name', $game_name);
		$this->db->delete($this->tableName);
	}
}
