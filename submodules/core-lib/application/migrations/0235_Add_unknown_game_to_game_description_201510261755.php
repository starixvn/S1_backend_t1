<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unknown_game_to_game_description_201510261755 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$this->db->insert('game_type', array(
			'game_platform_id' => MG_API,
			'game_type' => 'unknown', 'game_type_lang' => 'mg.unknown', 'status' => 1, 'flag_show_in_site' => 0,
		));
		$mg_game_type_id = $this->db->insert_id();

		$this->db->insert('game_type', array(
			'game_platform_id' => NT_API,
			'game_type' => 'unknown', 'game_type_lang' => 'nt.unknown', 'status' => 1, 'flag_show_in_site' => 0,
		));
		$nt_game_type_id = $this->db->insert_id();

		//Unknown game
		$this->db->insert($this->tableName, array(
			'game_code' => 'unknown', 'english_name' => "Unknown MG Game", 'external_game_id' => "",
			'game_platform_id' => MG_API, 'game_type_id' => $mg_game_type_id,
			'game_name' => 'mg.unknown', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));

		$this->db->insert($this->tableName, array(
			'game_code' => 'unknown', 'english_name' => "Unknown NT Game", 'external_game_id' => "",
			'game_platform_id' => NT_API, 'game_type_id' => $nt_game_type_id,
			'game_name' => 'nt.unknown', 'dlc_enabled' => 1, 'flash_enabled' => 1, 'mobile_enabled' => 1,
			'status' => 1, 'flag_show_in_site' => 0, 'game_order' => 0, 'no_cash_back' => 0, 'void_bet' => 0,
		));
	}

	public function down() {
		$this->db->query('delete from game_type where game_platform_id=? and game_type=?', array(MG_API, 'unknown'));
		$this->db->query('delete from game_type where game_platform_id=? and game_type=?', array(NT_API, 'unknown'));

		$this->db->query('delete from game_description where game_platform_id=? and game_code=?', array(MG_API, 'unknown'));
		$this->db->query('delete from game_description where game_platform_id=? and game_code=?', array(NT_API, 'unknown'));

	}
}