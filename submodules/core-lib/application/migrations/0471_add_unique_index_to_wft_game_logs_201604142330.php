<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_wft_game_logs_201604142330 extends CI_Migration {

	public function up() {
		# Change the field length first before making it an index
		$this->db->query('ALTER TABLE `wft_game_logs` CHANGE COLUMN `fetch_id` `fetch_id` VARCHAR(10) NULL DEFAULT NULL');
		$this->db->query('create unique index wft_unique_fetch_id on wft_game_logs(fetch_id)');
	}

	public function down() {
		$this->db->query('drop index wft_unique_fetch_id on wft_game_logs');
	}
}
