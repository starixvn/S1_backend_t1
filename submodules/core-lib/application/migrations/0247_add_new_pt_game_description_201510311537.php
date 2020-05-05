<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_pt_game_description_201510311537 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		// //King Derby
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'kgdb', 'game_name' => "pt.kgdb", 'external_game_id' => "kgdb",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 4,
		// ));
		// //Pinball Roulette
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'pbro', 'game_name' => "pt.pbro", 'external_game_id' => "pbro",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 5,
		// ));
		// //Ice Run
		// $this->db->insert($this->tableName, array(
		// 	'game_code' => 'gtsir', 'game_name' => "pt.gtsir", 'external_game_id' => "gtsir",
		// 	'game_platform_id' => PT_API, 'game_type_id' => 7,
		// ));
	}

	public function down() {
		// $codes = array('kgdb', 'pbro', 'gtsir');
		// $this->db->where_in('game_code', $codes);
		// $this->db->where('game_platform_id', PT_API);
		// $this->db->delete($this->tableName);
	}
}