<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_vr_game_logs_20180523 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_issuenumber_state on vr_game_logs(issueNumber, state, playerName)');
	}

	public function down() {
		$this->db->query('drop index idx_issuenumber_state on vr_game_logs');
	}
}

///END OF FILE//////////