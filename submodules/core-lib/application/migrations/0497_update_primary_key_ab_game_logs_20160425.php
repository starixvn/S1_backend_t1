<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_primary_key_ab_game_logs_20160425 extends CI_Migration {

	public function up() {
		$this->db->query('ALTER TABLE `ab_game_logs` ADD UNIQUE INDEX (`betNum`)');
	}

	public function down() {
		$this->db->query('drop index gameRoundId on ab_game_logs');
	}
}