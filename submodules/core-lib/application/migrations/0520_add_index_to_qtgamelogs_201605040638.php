<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_qtgamelogs_201605040638 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_response_result_id on qt_game_logs(response_result_id)');
		$this->db->query('create index idx_external_uniqueid  on qt_game_logs(external_uniqueid)');
		$this->db->query('create index idx_trans_id on qt_game_logs(transId)');
	}

	public function down() {
		$this->db->query('drop index idx_response_result_id on qt_game_logs');
		$this->db->query('drop index idx_external_uniqueid on qt_game_logs');
		$this->db->query('drop index idx_trans_id on qt_game_logs');
	}
}

///END OF FILE//////////