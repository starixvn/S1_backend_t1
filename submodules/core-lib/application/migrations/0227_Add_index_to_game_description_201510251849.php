<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_game_description_201510251849 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_game_platform_id on game_description(game_platform_id)');
		$this->db->query('create index idx_game_type_id on game_description(game_type_id)');
		$this->db->query('create index idx_game_code on game_description(game_code)');
		$this->db->query('create index idx_game_order on game_description(game_order)');
		$this->db->query('create index idx_english_name on game_description(english_name)');
	}

	public function down() {
		$this->db->query('drop index idx_game_platform_id on game_description');
		$this->db->query('drop index idx_game_type_id on game_description');
		$this->db->query('drop index idx_game_code on game_description');
		$this->db->query('drop index idx_game_order on game_description');
		$this->db->query('drop index idx_english_name on game_description');
	}
}

///END OF FILE//////////