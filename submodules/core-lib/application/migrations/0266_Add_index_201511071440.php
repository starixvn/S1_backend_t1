<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_201511071440 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_player_id on playerlevel(playerId)');
		$this->db->query('create index idx_level_id on playerlevel(playerGroupId)');
	}

	public function down() {
		$this->db->query('drop index idx_player_id on playerlevel');
		$this->db->query('drop index idx_level_id on playerlevel');
	}
}

///END OF FILE//////////