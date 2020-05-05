<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_kenogame_unknown_in_game_description_201604131048 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	private $tableName = 'game_type';

	public function up() {
	

	  $this->db->trans_start();
		

		$this->db->insert('game_type', array(
			'game_platform_id' => KENOGAME_API,
			'game_type' => 'unknown',
			'game_type_lang' => 'kenogame.unknown',
			'status' => self::FLAG_TRUE,
			'flag_show_in_site' =>   self::FLAG_TRUE
			));


		$lastId =  $this->db->insert_id();

		$data = array(
               'game_type_id' => $lastId ,
               'game_name' => 'kenogame.unknown'
               );

		$where =  array( 'game_platform_id' => KENOGAME_API,'game_code' => 'unknown');
        $this->db->where($where);
		$this->db->update('game_description',$data); 



		$this->db->trans_complete();
	}

	public function down() {
		
		$this->db->trans_start();
		$this->db->delete('game_type', array('game_platform_id' => KENOGAME_API, 'game_type' => 'unknown')); 
		$data = array(
               'game_type_id' => '0' 
               );
	
		$where =  array( 'game_platform_id' => KENOGAME_API,'game_code' => 'unknown');
        $this->db->where($where);
		$this->db->update('game_description', $data); 
		$this->db->trans_complete();
	}
}



