<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_game_description_201510261134 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_external_game_id on game_description(external_game_id)');

		$this->db->query('update game_description set game_code=LOWER(game_code) where game_platform_id=6');
	}

	public function down() {
		$this->db->query('drop index idx_external_game_id on game_description');
	}
}

///END OF FILE//////////