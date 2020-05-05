<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_index_to_game_description_201512261540 extends CI_Migration {

	public function up() {
		$this->db->query('drop index idx_un_pcmid on game_description');
		$this->db->query('create index idx_moduleid_clientid on game_description(game_platform_id,clientid,moduleid)');
	}

	public function down() {
		// $this->db->query('drop index idx_game_platform_id on game_description');
	}
}

///END OF FILE//////////