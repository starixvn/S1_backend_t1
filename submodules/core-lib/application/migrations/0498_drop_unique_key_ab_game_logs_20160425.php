<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_unique_key_ab_game_logs_20160425 extends CI_Migration {

	public function up() {
		$this->db->query('drop index gameRoundId on ab_game_logs');
	}

	public function down() {
	}
}