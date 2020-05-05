<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_transactionid_to_png_game_logs_20180204 extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_transactionid on png_game_logs(TransactionId)');
	}

	public function down() {
		$this->db->query('drop index idx_transactionid on png_game_logs');
	}
}

///END OF FILE//////////