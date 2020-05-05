<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_impt_in_game_20160420 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {
		// $this->db->trans_start();
		// $this->db->delete($this->tableName, array('id' => IMPT_API));
		// $this->db->delete('game', array('gameId' => IMPT_API));
		// $this->db->insert($this->tableName, array(
		// 	"id" => IMPT_API, "system_name" => "IMPT_API", "system_code" => 'IMPT',
		// 	'system_type' => SYSTEM_GAME_API, "live_mode" => 0,
		// 	"class_name" => "game_api_impt", 'local_path' => 'game_platform', 'manager' => 'game_platform_manager'));
		// $this->db->insert('game', array("gameId" => IMPT_API, "game" => "IMPT"));
		// $this->db->trans_complete();
	}

	public function down() {
	}
}
