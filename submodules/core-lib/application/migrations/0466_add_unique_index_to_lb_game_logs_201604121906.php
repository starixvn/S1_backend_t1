<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_lb_game_logs_201604121906 extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_unique_bet_id on lb_game_logs(bet_id)');
		// $this->db->query('create unique index idx_unique_external_uniqueid on lb_game_logs(external_uniqueid)');
	}

	public function down() {
		$this->db->query('drop index idx_unique_bet_id on lb_game_logs');
		// $this->db->query('drop index idx_unique_external_uniqueid on lb_game_logs');
	}
}

///END OF FILE//////////