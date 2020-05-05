<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_from_betmaster_game_logs_201706161200 extends CI_Migration {

	private $tableName = 'betmaster_game_logs';

	public function up() {
        $this->db->query('drop index idx_response_result_id on betmaster_game_logs');
	}

	public function down() {
        $this->db->query('create unique index idx_response_result_id on betmaster_game_logs(response_result_id)');
	}
}